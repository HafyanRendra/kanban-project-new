<div class="task-progress-card">
  <div class="task-progress-card-left">
    @can('update', $task)
    @if ($task->status == 'completed')
      <div  class="material-icons task-progress-card-top-checked">check_circle</div>
    @else
      <a style= "text-decoration:none" href="{{route('tasks.updateStatusCardBlade',['id'=>$task->id])}}" class="material-icons task-progress-card-top-check">check_circle</a>
    @endif
    <a href="{{ route('tasks.edit', ['id' => $task->id]) }}" class="material-icons task-progress-card-top-edit">more_vert</a>
  </div>
  <p class="task-progress-card-title">{{ $task->name }}</p>
  <div>
    <p>{{ $task->detail }}</p>
    @endcan
  </div>
  <div>
    <p>Due on {{ $task->due_date }}</p>
  </div>
  <div>
    <p>Owner: <strong>{{ $task->user->name }}</strong></p>
  </div>
  <div class="@if ($leftStatus) task-progress-card-left @else task-progress-card-right @endif">
    @if ($leftStatus)
     
      <form
        action="{{ route('tasks.move', ['id' => $task->id, 'status' => $leftStatus]) }}" 
        method="POST"
      >
        @method('patch')
        @csrf
        <button class="material-icons">chevron_left</button>
      </form>
    @endif

    @if ($rightStatus)
     
      <form
        action="{{ route('tasks.move', ['id' => $task->id, 'status' => $rightStatus]) }}"
        method="POST"
      >
        @method('patch')
        @csrf
        <button class="material-icons">chevron_right</button>
      </form>
    @endif
  </div>

  <div>
    <p>Owner: <strong>{{ $task->user->name }}</strong></p>
  </div>

  @if ($task->files)
    <div>
      @foreach ($task->files as $file)
      <div class="task-progress-card-file">
        <span class="material-icons">file_open</span>
        <a
          target="_blank"
          href="{{ route('tasks.files.show', ['task_id' => $task->id, 'id' => $file->id]) }}"
        >
          {{ $file->filename }}
        </a>
      </div>
      @endforeach
    </div>
  @endif
</div>