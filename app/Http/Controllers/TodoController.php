<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\Task;
use Carbon\Carbon;

class TodoController extends Controller
{

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
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

        $query = Task::where('user_id', $user->id);

        if ($request->has('filter')) {
            if ($request->filter == 'done') {
                $query->where('status', 'Done');
            } elseif ($request->filter == 'inprogress') {
                $query->where('status', 'In progress');
            }
        }

        $tasks = $query->orderBy('date', 'desc')->orderBy('id', 'asc')->paginate(8);

        $totalTasks = Task::where('user_id', $user->id)->count();
        $doneTasks = Task::where('user_id', $user->id)->where('status', 'Done')->count();
        $inProgressTasks = Task::where('user_id', $user->id)->where('status', 'In progress')->count();

        return view('todo', compact('tasks', 'totalTasks', 'doneTasks', 'inProgressTasks'));
    }
}
