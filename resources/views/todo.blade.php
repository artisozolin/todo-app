@include('partials.header')

<div class="todo-container">
    <div class="todo-list-title">
        Todo List
    </div>
    <form action="{{ route('tasks.store') }}" method="POST" class="add-task-form">
        @csrf
        <div class="add-task-container">
            <input type="text" name="title" placeholder="Title" id="todoTitle" class="add-task-input"
                   required maxlength="255"/>
            <div id="todoTitleError" class="add-task-error hidden"></div>
            <input type="text" name="description" placeholder="Description" id="todo" class="add-task-input mt-6"
                   required maxlength="255"/>
            <div id="todoError" class="add-task-error hidden"></div>
        </div>
        <button type="submit" id="todoSubmit" class="add-task-button">
            Add task
        </button>
    </form>
    @php
        $currentFilter = request('filter');
    @endphp
    <div class="task-stats-container">
        <a href="{{ url('/?filter=all') }}"
           class="task-stats {{ $currentFilter == 'all' ? 'outline outline-stone-950 outline-solid' : '' }}">
            <div>Total</div>
            <div>{{ $totalTasks }}</div>
        </a>
        <a href="{{ url('/?filter=done') }}"
           class="task-stats {{ $currentFilter == 'done' ? 'outline outline-stone-950 outline-solid' : '' }}">
            <div>Done</div>
            <div>{{ $doneTasks }}</div>
        </a>
        <a href="{{ url('/?filter=inprogress') }}"
           class="task-stats {{ $currentFilter == 'inprogress' ? 'outline outline-stone-950 outline-solid' : '' }}">
            <div>In progress</div>
            <div>{{ $inProgressTasks }}</div>
        </a>
    </div>
    <div class="task-list-container">
        @foreach ($tasks as $task)
            <div class="task-container"
                 onclick="openModal({{ $task }})">
                <div class="task-description">
                    {{ $task->title }}
                </div>
                <div class="task-status-container">
                    <div class="task-add-date">
                        {{ \Carbon\Carbon::parse($task->date)->format('M jS') }}
                    </div>
                    <div class="task-status {{ $task->status === 'Done' ? 'bg-todo-green' :
                            ($task->status === 'In progress' ? 'bg-todo-yellow' : 'bg-gray-300') }}">
                        {{ $task->status }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if ($tasks->hasPages())
        <div class="todo-pagination-container">
            @if ($tasks->onFirstPage())
                <div class="todo-pagination-button-inactive">
                    &lt;
                </div>
            @else
                <a href="{{ $tasks->appends(request()->query())->previousPageUrl() }}"
                   class="todo-pagination-button-active">
                    &lt;
                </a>
            @endif

            <div class="todo-pagination-current-page">
                {{ $tasks->currentPage() }}
            </div>

            @if ($tasks->hasMorePages())
                <a href="{{ $tasks->appends(request()->query())->nextPageUrl() }}"
                   class="todo-pagination-button-active">
                    &gt;
                </a>
            @else
                <div class="todo-pagination-button-inactive">
                    &gt;
                </div>
            @endif
        </div>
    @endif
    <div id="taskModal" class="todo-modal hidden">
        <div class="todo-modal-container">
            <div class="todo-modal-header">
                <h2 class="todo-modal-title">Edit Task</h2>
                <button onclick="closeModal()" class="todo-modal-exit-button">
                    &times;
                </button>
            </div>

            <form id="taskForm" method="POST" class="todo-modal-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="task_id" id="task_id">

                <label class="todo-modal-label">Title:</label>
                <textarea type="text" name="title" id="modalTitle" maxlength="255" class="todo-modal-input"></textarea>
                <div id="modalTitleError" class="todo-modal-error hidden"></div>

                <label class="todo-modal-label">Description:</label>
                <textarea type="text" name="description" id="description" maxlength="255"
                          class="todo-modal-input">
                        </textarea>
                <div id="descriptionError" class="todo-modal-error hidden"></div>

                <label class="todo-modal-label">Status:</label>
                <select name="status" id="status" class="todo-modal-input">
                    <option value="In progress">In Progress</option>
                    <option value="Done">Done</option>
                </select>

                <label class="todo-modal-label">Date:</label>
                <input type="datetime-local" name="date" id="date" class="todo-modal-input">

                <div class="todo-modal-footer">
                    <button type="submit" id="modalSubmit" class="todo-modal-submit">
                        Save
                    </button>
                    <button type="button" onclick="deleteTask()" class="todo-modal-delete">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function openModal(task) {
        document.getElementById('taskModal').classList.remove('hidden');
        document.getElementById('task_id').value = task.id;
        document.getElementById('modalTitle').value = task.title;
        document.getElementById('description').value = task.description;
        document.getElementById('status').value = task.status;
        document.getElementById('date').value = new Date(task.date).toISOString().slice(0, 16);

        document.getElementById('taskForm').action = `/tasks/${task.id}`;
    }

    function closeModal() {
        document.getElementById('taskModal').classList.add('hidden');
    }

    function deleteTask() {
        const taskId = document.getElementById('task_id').value;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tasks/${taskId}`;
        form.innerHTML = `
                @csrf
        @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }

    function validateField(input, errorElement, submitButton) {
        const forbiddenPattern = /['";=]|--/;
        const value = input.value.trim();
        let errorMessage = "";

        if (value === "") {
            errorMessage = "This field cannot be empty.";
        } else if (forbiddenPattern.test(value)) {
            errorMessage = "Invalid characters detected (quotes, semicolon, etc).";
        }

        if (errorMessage) {
            input.classList.add('invalid-input');
            errorElement.textContent = errorMessage;
            errorElement.classList.remove('hidden');
            submitButton.disabled = true;
            submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.remove('bg-todo-lightGreen');
            return false;
        } else {
            input.classList.remove('invalid-input');
            errorElement.classList.add('hidden');
            submitButton.disabled = false;
            submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.add('bg-todo-lightGreen');
            return true;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form[action="{{ route('tasks.store') }}"]');
        const titleInput = document.getElementById('todoTitle');
        const titleError = document.getElementById('todoTitleError');
        const descriptionInput = document.getElementById('todo');
        const todoError = document.getElementById('todoError');
        const submitButton = document.getElementById('todoSubmit');

        titleInput.addEventListener('input', () => validateField(titleInput, titleError, submitButton));
        descriptionInput.addEventListener('input', () => validateField(descriptionInput, todoError, submitButton));

        form.addEventListener('submit', function (e) {
            const isTitleValid = validateField(titleInput, titleError, submitButton);
            const isDescriptionValid = validateField(descriptionInput, todoError, submitButton);
            if (!isTitleValid || !isDescriptionValid) {
                e.preventDefault();
            }
        });

        const modalForm = document.getElementById('taskForm');
        const modalTitleInput = document.getElementById('modalTitle');
        const modalTitleError = document.getElementById('modalTitleError');
        const modalDescriptionInput = document.getElementById('description');
        const modalDescriptionError = document.getElementById('descriptionError');
        const modalSubmitButton = document.getElementById('modalSubmit');

        modalTitleInput.addEventListener('input', () => validateField(modalTitleInput, modalTitleError, modalSubmitButton));
        modalDescriptionInput.addEventListener('input', () => validateField(modalDescriptionInput, modalDescriptionError, modalSubmitButton));

        modalForm.addEventListener('submit', function (e) {
            const isTitleValid = validateField(modalTitleInput, modalTitleError, modalSubmitButton);
            const isDescriptionValid = validateField(modalDescriptionInput, modalDescriptionError, modalSubmitButton);
            if (!isTitleValid || !isDescriptionValid) {
                e.preventDefault();
            }
        });
    });
</script>

@include('partials.footer')
