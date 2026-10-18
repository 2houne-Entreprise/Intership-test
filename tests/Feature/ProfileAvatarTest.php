<?php

use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores a profile photo and exposes it in the app shell', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $photo = UploadedFile::fake()->create('avatar.png', 120, 'image/png');

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $photo,
        ])
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull()
        ->and(Storage::disk('public')->exists($user->profile_photo_path))->toBeTrue();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('profile-photo')
        ->assertSee(asset('storage/'.$user->profile_photo_path), false);
});

it('replaces and deletes only the current users photo', function () {
    Storage::fake('public');
    Storage::disk('public')->put('profile-photos/old.png', 'old photo');
    Storage::disk('public')->put('profile-photos/other.png', 'other photo');
    $user = User::factory()->create(['profile_photo_path' => 'profile-photos/old.png']);
    $other = User::factory()->create(['profile_photo_path' => 'profile-photos/other.png']);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'photo' => UploadedFile::fake()->create('replacement.png', 120, 'image/png'),
    ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

    $newPath = $user->refresh()->profile_photo_path;
    Storage::disk('public')->assertMissing('profile-photos/old.png');
    Storage::disk('public')->assertExists($newPath);

    $this->delete(route('profile.photo.destroy'))->assertRedirect(route('profile.edit'));
    expect($user->refresh()->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($newPath);
    Storage::disk('public')->assertExists($other->profile_photo_path);
});

it('rejects invalid photos without changing the existing photo', function (string $filename, int $size, string $mime) {
    Storage::fake('public');
    Storage::disk('public')->put('profile-photos/current.png', 'current photo');
    $user = User::factory()->create(['profile_photo_path' => 'profile-photos/current.png']);

    $this->actingAs($user)->patch(route('profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'photo' => UploadedFile::fake()->create($filename, $size, $mime),
    ])->assertSessionHasErrors('photo');

    expect($user->refresh()->profile_photo_path)->toBe('profile-photos/current.png');
    Storage::disk('public')->assertExists('profile-photos/current.png');
})->with([
    'document' => ['document.pdf', 100, 'application/pdf'],
    'oversized' => ['large.png', 2049, 'image/png'],
    'unsafe extension' => ['avatar.php', 100, 'image/png'],
]);

it('shows owned and shared projects but excludes private projects and pending invitees', function () {
    $viewer = User::factory()->create();
    $owner = User::factory()->create(['name' => 'Shared Owner']);
    $pending = User::factory()->create(['name' => 'Pending Invitee']);
    $stranger = User::factory()->create(['name' => 'Private Owner']);
    $viewer->projects()->create(['name' => 'Owned Project']);
    $shared = $owner->projects()->create(['name' => 'Shared Project']);
    $shared->members()->attach($viewer, ['role' => 'viewer']);
    $shared->tasks()->createMany([
        ['title' => 'Completed task', 'status' => 'done'],
        ['title' => 'Pending task', 'status' => 'pending'],
    ]);
    ProjectInvitation::create([
        'project_id' => $shared->id,
        'inviter_id' => $owner->id,
        'invited_user_id' => $pending->id,
        'role' => 'editor',
        'status' => 'pending',
    ]);
    $private = $stranger->projects()->create(['name' => 'Secret Project']);

    $this->actingAs($viewer)->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('Owned Project')->assertSee('Shared Project')
        ->assertSee('Shared Owner')->assertSee('Viewer')->assertSee('50%')
        ->assertDontSee('Secret Project')->assertDontSee('Private Owner')->assertDontSee('Pending Invitee');
    $this->get(route('projects.show', $private))->assertForbidden();
});

it('limits the visible avatar group to four people and counts the rest', function () {
    $owner = User::factory()->create();
    $project = $owner->projects()->create(['name' => 'Large Team']);
    $members = User::factory()->count(5)->create();
    foreach ($members as $member) {
        $project->members()->attach($member, ['role' => 'editor']);
    }

    $this->actingAs($owner)->get(route('projects.index'))->assertSuccessful()
        ->assertSee('+2')->assertSee($members[2]->name)->assertDontSee($members[3]->name);
});

it('shows accepted project members as an avatar group on the project list', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = $owner->projects()->create([
        'name' => 'StudyTime',
        'description' => 'Planning and study tracking.',
    ]);
    $project->members()->attach($member, ['role' => 'editor']);

    $this->actingAs($owner)
        ->get(route('projects.index'))
        ->assertSuccessful()
        ->assertSee('project-member-avatars')
        ->assertSee('Owner')
        ->assertSee('Editor')
        ->assertSee($member->name);
});
