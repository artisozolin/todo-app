@include('partials.header')

<div class="todo-container">
    <div class="todo-list-title">
        Todo List
    </div>
    <form id="taskFormMain" action="{{ route('tasks.store') }}" method="POST" class="add-task-form">
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
    function validateField(input, errorElement, touched) {
        const forbiddenPattern = /['";=]|--/;
        const value = input.value.trim();
        let errorMessage = "";

        if (value === "") {
            errorMessage = "This field cannot be empty.";
        } else if (forbiddenPattern.test(value)) {
            errorMessage = "Invalid characters detected (quotes, semicolon, etc).";
        }

        const isValid = errorMessage === "";

        if (!isValid && touched[input.id]) {
            input.classList.add('invalid-input');
            errorElement.textContent = errorMessage;
            errorElement.classList.remove('hidden');
        } else {
            input.classList.remove('invalid-input');
            errorElement.classList.add('hidden');
        }

        return isValid;
    }

    function updateSubmitButtonState(isFormValid, submitButton) {
        submitButton.disabled = !isFormValid;
        if (isFormValid) {
            submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.add('bg-todo-green');
        } else {
            submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.remove('bg-todo-green');
        }
    }

    function setupFormValidation({ formId, inputs, errorIds, submitButtonId }) {
        const form = document.getElementById(formId);
        const submitButton = document.getElementById(submitButtonId);

        const inputElements = inputs.map(id => document.getElementById(id));
        const errorElements = errorIds.map(id => document.getElementById(id));

        const touched = Object.fromEntries(inputs.map(id => [id, false]));

        submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');

        inputElements.forEach((input) => {
            input.addEventListener('input', () => {
                if (!touched[input.id]) touched[input.id] = true;

                const isValid = inputElements.map((inp, i) =>
                    validateField(inp, errorElements[i], touched, submitButton)
                ).every(Boolean);

                updateSubmitButtonState(isValid, submitButton);
            });
        });

        form.addEventListener('submit', (e) => {
            inputs.forEach(id => touched[id] = true);

            const isValid = inputElements.map((inp, i) =>
                validateField(inp, errorElements[i], touched, submitButton)
            ).every(Boolean);

            updateSubmitButtonState(isValid, submitButton);

            if (!isValid) e.preventDefault();
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupFormValidation({
            formId: 'taskFormMain',
            inputs: ['todoTitle', 'todo'],
            errorIds: ['todoTitleError', 'todoError'],
            submitButtonId: 'todoSubmit',
        });

        setupFormValidation({
            formId: 'taskForm',
            inputs: ['modalTitle', 'description'],
            errorIds: ['modalTitleError', 'descriptionError'],
            submitButtonId: 'modalSubmit',
        });
    });
</script>

@include('partials.footer')
