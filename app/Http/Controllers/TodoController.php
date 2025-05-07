<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TodoController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('date', 'desc')->get();

        $totalTasks = $tasks->count();
        $doneTasks = $tasks->where('status', 'Done')->count();
        $inProgressTasks = $tasks->where('status', 'In progress')->count();

        return view('todo', compact('tasks', 'totalTasks', 'doneTasks', 'inProgressTasks'));
    }
}
