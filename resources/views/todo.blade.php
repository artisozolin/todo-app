<!DOCTYPE html>
<html class="h-screen" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet" />
    <title>Todo app</title>
</head>
<body class="bg-gradient-to-b from-todo-lightPink">
    <div class="m-auto w-xl mt-12">
        <div class="m-auto max-w-fit font-barlowMedium text-4xl mb-6">
            Todo List
        </div>
        <div class="flex font-barlow text-xl items-center">
            <div class="ml-auto w-full h-10">
                <input type="text" id="todo" class="pl-2 rounded-xl shadow-md w-full h-full">
            </div>
            <button class="mr-auto ml-2 w-32 h-10 bg-todo-lightGreen rounded-xl shadow-md">
                Add task
            </button>
        </div>
        <div class="task-stats text-center flex justify-between mt-6">
            <div class="w-1/3 mr-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Total tasks</div>
                <div>103</div>
            </div>
            <div class="w-1/3 mx-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Tasks done</div>
                <div>3</div>
            </div>
            <div class="w-1/3 ml-4 py-2 bg-todo-grey rounded-xl shadow-md">
                <div>Tasks in progress</div>
                <div>7</div>
            </div>
        </div>
        <div class="task-pagination-by-day flex">
            <div class="back-button"></div>
            <div class="date"></div>
            <div class="forward-button"></div>
        </div>
        <div class="task-list text-base">
            <div class="task w-full bg-white rounded-xl h-14 flex mt-6 shadow-md justify-between">
                <div class="content-center pl-2">
                    Task description
                </div>
                <div class="flex">
                    <div class="content-center text-xs">
                        May 7th 18:22
                    </div>
                    <div class="w-[102px] rounded-tr-xl rounded-br-xl content-center bg-todo-green ml-2 text-center">
                        Done
                    </div>
                </div>
            </div>
            <div class="task w-full bg-white rounded-xl h-14 flex mt-6 shadow-md justify-between">
                <div class="content-center pl-2">
                    Task description
                </div>
                <div class="flex">
                    <div class="content-center text-xs">
                        May 6th 21:32
                    </div>
                    <div class="w-[102px] rounded-tr-xl rounded-br-xl content-center bg-todo-yellow ml-2 text-center">
                        In progress
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
