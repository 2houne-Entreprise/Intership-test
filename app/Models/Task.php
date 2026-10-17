<?php

namespace App\Models;

use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'status', 'deadline'])]
class Task extends Model
{
    public const array STATUSES = ['pending', 'in_progress', 'done'];

    public const array STATUS_LABELS = [
        'pending' => 'En attente',
        'in_progress' => 'En cours',
        'done' => 'Terminée',
    ];

    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    /**
     * @param  Builder<Task>  $query
     * @return Builder<Task>
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('deadline', '<', today()->toDateString())->where('status', '!=', 'done');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? 'Inconnu';
    }

    /** @return BelongsTo<Project, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['deadline' => 'date'];
    }
}
