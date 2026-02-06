@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="mb-3">Tasks</h1>

            <form method="GET" action="{{ route('tasks.index') }}" class="form-inline mb-3">
                <div class="input-group">
                    <input type="search" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Search by title">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary ml-2">Create Task</a>
            </form>

            @if($tasks->isEmpty())
                <div class="alert alert-info">No tasks found.</div>
            @else
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td style="max-width:300px;"><div class="text-truncate d-inline-block" style="max-width:280px;">{{ $task->title }}<br><small class="text-muted">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</small></div></td>
                            <td>{{ $task->created_at->format('Y-m-d') }}</td>
                            <td>
                                @if(strtolower($task->status) === 'completed' || $task->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <!-- Desktop / larger screens: show inline buttons -->
                                <div class="d-none d-sm-inline-block">
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary" aria-label="Edit task {{ $task->title }}">Edit</a>
                                    <button class="btn btn-sm btn-outline-success toggle-status" data-id="{{ $task->id }}" aria-label="Toggle status for {{ $task->title }}">{{ strtolower($task->status) === 'completed' ? 'Mark Pending' : 'Mark Completed' }}</button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal('{{ route('tasks.destroy', $task) }}')" aria-label="Delete task {{ $task->title }}">Delete</button>
                                </div>

                                <!-- Mobile: collapse into dropdown -->
                                <div class="d-inline-block d-sm-none">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown{{ $task->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Actions for {{ $task->title }}">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="actionsDropdown{{ $task->id }}">
                                            <a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">Edit</a>
                                            <button class="dropdown-item ajax-toggle" data-id="{{ $task->id }}" aria-label="Toggle status for {{ $task->title }}">{{ strtolower($task->status) === 'completed' ? 'Mark Pending' : 'Mark Completed' }}</button>
                                            <button class="dropdown-item text-danger" onclick="openDeleteModal('{{ route('tasks.destroy', $task) }}')" aria-label="Delete task {{ $task->title }}">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this task?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(action) {
        var f = document.getElementById('deleteForm');
        f.action = action;
        $('#deleteModal').modal('show');
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showJsAlert(message, type = 'danger'){
        const container = document.getElementById('jsAlertContainer');
        const id = 'js-alert-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = `alert alert-${type}`;
        div.setAttribute('role', 'status');
        div.textContent = message;
        container.appendChild(div);
        // auto-remove after 4s
        setTimeout(() => { if(div.parentNode) div.parentNode.removeChild(div); }, 4000);
    }

    function bindToggleButtons(selector){
        document.querySelectorAll(selector).forEach(function(btn){
            btn.addEventListener('click', function(){
                const id = this.dataset.id;
                const url = '/tasks/' + id + '/toggle';

                // optimistic UI: flip UI right away and revert on error
                const row = btn.closest('tr');
                const badge = row.querySelector('.badge');
                const wasCompleted = badge && badge.textContent.trim().toLowerCase() === 'completed';
                // apply optimistic change
                if(badge){
                    if(wasCompleted){
                        badge.className = 'badge badge-secondary';
                        badge.textContent = 'Pending';
                    } else {
                        badge.className = 'badge badge-success';
                        badge.textContent = 'Completed';
                    }
                }
                // update button texts immediately
                document.querySelectorAll('[data-id="' + id + '"]').forEach(function(b){
                    if(b.classList.contains('toggle-status') || b.classList.contains('ajax-toggle')){
                        b.textContent = wasCompleted ? 'Mark Completed' : 'Mark Pending';
                    }
                });

                // send request
                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                }).then(r => {
                    if(!r.ok) throw new Error('network');
                    return r.json();
                }).then(data => {
                    // ensure UI matches server response
                    if(badge){
                        if(data.status === 'completed'){
                            badge.className = 'badge badge-success';
                            badge.textContent = 'Completed';
                        } else {
                            badge.className = 'badge badge-secondary';
                            badge.textContent = 'Pending';
                        }
                    }
                    document.querySelectorAll('[data-id="' + id + '"]').forEach(function(b){
                        if(b.classList.contains('toggle-status') || b.classList.contains('ajax-toggle')){
                            b.textContent = data.status === 'completed' ? 'Mark Pending' : 'Mark Completed';
                        }
                    });
                }).catch(e => {
                    // revert optimistic UI
                    if(badge){
                        if(wasCompleted){
                            badge.className = 'badge badge-success';
                            badge.textContent = 'Completed';
                        } else {
                            badge.className = 'badge badge-secondary';
                            badge.textContent = 'Pending';
                        }
                    }
                    document.querySelectorAll('[data-id="' + id + '"]').forEach(function(b){
                        if(b.classList.contains('toggle-status') || b.classList.contains('ajax-toggle')){
                            b.textContent = wasCompleted ? 'Mark Pending' : 'Mark Completed';
                        }
                    });
                    showJsAlert('Could not update status. Please try again.', 'danger');
                });
            });
        });
    }

    bindToggleButtons('.toggle-status');
    bindToggleButtons('.ajax-toggle');
});
</script>
@endsection
