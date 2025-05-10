<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;

class TodoController extends Controller
{
    public function index()
    {
        $cookieName = 'user_cookie';
        $cookieValue = Cookie::get($cookieName);
        $cookieExpirationTime = 60 * 24 * 30;

        if (!$cookieValue || !($user = User::where('generated_cookie', $cookieValue)->first())) {
            $cookieValue = Str::uuid();
            $user = User::create([
                'generated_cookie' => $cookieValue,
                'cookie_date' => now()->addDays(30),
                'status' => 'active',
            ]);

            Cookie::queue($cookieName, $cookieValue, $cookieExpirationTime);
        } else {
            $user->update(['cookie_date' => now()->addDays(30)]);
            Cookie::queue($cookieName, $cookieValue, $cookieExpirationTime);
        }

        $tasks = Task::where('user_id', $user->id)->orderBy('date', 'desc')->get();

        $totalTasks = $tasks->count();
        $doneTasks = $tasks->where('status', 'Done')->count();
        $inProgressTasks = $tasks->where('status', 'In progress')->count();

        return view('todo', compact('tasks', 'totalTasks', 'doneTasks', 'inProgressTasks'));
    }
}
