<!DOCTYPE html>
<html class="h-screen" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <title>Todo app</title>
</head>
<body class="bg-gradient-to-b from-todo-lightPink">
    <div class="m-auto w-xl mt-6">
        <div class="m-auto max-w-fit font-barlowMedium text-4xl mb-6">
            Todo List
        </div>
        <form action="{{ route('tasks.store') }}" method="POST" class="flex font-barlow text-xl items-center">
            @csrf
            <div class="ml-auto w-full h-10">
                <input type="text" name="description" id="todo" class="pl-2 rounded-xl shadow-md w-full h-full" required>
            </div>
            <button type="submit" class="mr-auto ml-2 w-32 h-10 bg-todo-lightGreen rounded-xl shadow-md">
                Add task
            </button>
        </form>
        <div class="task-stats text-center flex justify-between mt-6">
            <div class="w-1/3 mr-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Total tasks</div>
                <div>{{ $totalTasks }}</div>
            </div>
            <div class="w-1/3 mx-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Tasks done</div>
                <div>{{ $doneTasks }}</div>
            </div>
            <div class="w-1/3 ml-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Tasks in progress</div>
                <div>{{ $inProgressTasks }}</div>
            </div>
        </div>
        <div class="task-pagination-by-day flex">
            <div class="back-button"></div>
            <div class="date"></div>
            <div class="forward-button"></div>
        </div>
        <div class="task-list text-base">
            @foreach ($tasks as $task)
                <div class="task w-full bg-white rounded-xl h-14 flex mt-6 shadow-md justify-between outline-2
                outline-offset-1 hover:outline hover:outline-stone-950 hover:outline-solid cursor-pointer"
                onclick="openModal({{ $task }})">
                    <div class="content-center pl-4">
                        {{ $task->description }}
                    </div>
                    <div class="flex">
                        <div class="content-center text-xs">
                            {{ \Carbon\Carbon::parse($task->date)->format('M jS H:i') }}
                        </div>
                        <div class="w-[102px] rounded-tr-xl rounded-br-xl content-center border-l-2 border-stone-950
                    {{ $task->status === 'Done' ? 'bg-todo-green' : ($task->status === 'In progress' ? 'bg-todo-yellow' : 'bg-gray-300') }}
                            ml-2 text-center">
                            {{ $task->status }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($tasks->hasPages())
            <div class="flex justify-center items-center mt-6 space-x-4 text-lg font-medium">
                @if ($tasks->onFirstPage())
                    <div class="text-gray-400 text-2xl">&lt;</div>
                @else
                    <a href="{{ $tasks->previousPageUrl() }}" class="no-underline text-2xl">&lt;</a>
                @endif

                <div class="px-4 outline outline-stone-950 outline-solid rounded-xl">{{ $tasks->currentPage() }}</div>

                @if ($tasks->hasMorePages())
                    <a href="{{ $tasks->nextPageUrl() }}" class="no-underline text-2xl">&gt;</a>
                @else
                    <div class="text-gray-400 text-2xl">&gt;</div>
                @endif
            </div>
        @endif

        <div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 content-center">
            <div class="bg-white rounded-xl p-6 w-full max-w-md m-auto">
                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-semibold">Edit Task</h2>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
                </div>

                <form id="taskForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="task_id" id="task_id">

                    <label class="block mb-2">Description:</label>
                    <input type="text" name="description" id="description" class="w-full border rounded p-2 mb-4">

                    <label class="block mb-2">Status:</label>
                    <select name="status" id="status" class="w-full border rounded p-2 mb-4">
                        <option value="In progress">In Progress</option>
                        <option value="Done">Done</option>
                    </select>

                    <label class="block mb-2">Date:</label>
                    <input type="datetime-local" name="date" id="date" class="w-full border rounded p-2 mb-4">

                    <div class="flex justify-between">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                        <button type="button" onclick="deleteTask()" class="bg-red-500 text-white px-4 py-2 rounded">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openModal(task) {
            document.getElementById('taskModal').classList.remove('hidden');
            document.getElementById('task_id').value = task.id;
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
    </script>
</body>
</html>
