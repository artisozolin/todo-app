<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $cookieName = 'user_cookie';
        $cookieValue = $request->cookie($cookieName);
        $cookieExpirationTime = 60 * 24 * 30;
        $user = null;

        if (!$cookieValue || !($user = User::where('generated_cookie', $cookieValue)->first())) {
            $newCookie = Str::uuid()->toString();
            $user = User::create([
                'generated_cookie' => $newCookie,
                'cookie_date' => Carbon::now()->addDays(30),
                'status' => 'active',
            ]);

            Cookie::queue($cookieName, $newCookie, $cookieExpirationTime);
        } else {
            $user->update([
                'cookie_date' => Carbon::now()->addDays(30)
            ]);

            Cookie::queue($cookieName, $cookieValue, $cookieExpirationTime);
        }

        Task::create([
            'description' => $request->input('description'),
            'status' => $request->input('status', 'in progress'),
            'date' => now(),
            'user_id' => $user->id
        ]);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {
        $task->update($request->only(['description', 'status', 'date']));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }
}
