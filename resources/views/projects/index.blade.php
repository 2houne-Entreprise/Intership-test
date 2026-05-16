@foreach($projects as $project)
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">

        <h3>{{ $project->name }}</h3>

        <p>{{ $project->description }}</p>

        {{-- VIEW BUTTON (IMPORTANT FOR US3) --}}
        <a href="{{ route('projects.show', $project) }}">
            👁 View Project (Tasks)
        </a>

        <br><br>

        {{-- EDIT --}}
        <a href="{{ route('projects.edit', $project) }}">
            ✏️ Edit
        </a>

        {{-- DELETE --}}
        <form action="{{ route('projects.destroy', $project) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit">🗑 Delete</button>
        </form>

    </div>
@endforeach