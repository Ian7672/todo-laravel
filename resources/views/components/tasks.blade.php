<ul id="taskList">
    @foreach ($tasks->sortBy('completed') as $task)
    <li class="task-item" data-id="{{ $task->id_task }}">
        <div class="task-left">
            <div class="task-info">
                <!-- Checkbox completed -->
                <form method="POST" action="/tasks/{{ $task->id_task }}/toggle" class="checkbox-form">
                    @csrf
                    @method('PATCH')
                    <input 
                        type="checkbox" 
                        name="completed" 
                        {{ $task->completed ? 'checked' : '' }} 
                        aria-label="Tandai selesai: {{ $task->title }}"
                    >
                </form>

                <span class="task-title-truncate {{ $task->completed ? 'completed' : '' }}" title="{{ $task->title }}">
                    {{ $task->title }}
                </span>  
                
                <div class="m-btn">      
                    <span 
                        class="more-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#taskDetailModal" 
                        data-id="{{ $task->id_task }}"
                    >
                        More
                    </span>
                    <span 
                        class="status-badge completed-badge" 
                        style="{{ $task->completed ? 'display: inline;' : 'display: none;' }}"
                    >
                        Selesai
                    </span>
                </div>
            </div>
            
            @if($task->description)
            <span class="task-description-preview">{{ Str::limit($task->description, 50) }}</span>
            @endif
        </div>
        
        <div class="task-dates">
            <div>Dibuat: {{ \Carbon\Carbon::parse($task->created_at)->format('d M Y H:i') }}</div>
            <div>Diperbarui: {{ \Carbon\Carbon::parse($task->updated_at)->format('d M Y H:i') }}</div>
            <div>Deadline: {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y') }}</div>
        </div>
        
        <div class="actions">
            <button
                class="more-btn"
                data-bs-toggle="modal"
                data-bs-target="#taskDetailModal"
                data-id="{{ $task->id_task }}"
            >
                More
            </button>

            <button
                class="edit-btn"
                data-bs-toggle="modal"
                data-bs-target="#editTaskModal"
                data-id="{{ $task->id_task }}"
            >
                Edit
            </button>

<form method="POST" action="{{ route('tasks.destroy', $task->id_task) }}" class="delete-form" data-title="{{ $task->title }}">
    @csrf
    @method('DELETE')
    <button type="button" class="delete-btn btn btn-danger" data-id="{{ $task->id_task }}" data-title="{{ $task->title }}">Hapus</button>
</form>

        </div>
    </li>
    @endforeach
</ul>

@include('modal.delete')
