<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $cookieName = 'user_cookie';
        $cookieValue = $request->cookie($cookieName);
        $cookieExpirationTime = 60 * 24 * 30;
        $user = null;

        $validatedData = $request->validate([
            'description' => [
                'required',
                'string',
                'max:255',
                'not_regex:/[\'";=]|--/'
            ],
            'status' => 'nullable|string',
        ]);

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
            'description' => $validatedData['description'],
            'status' => $validatedData['status'] ?? 'in progress',
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
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $validatedData = $request->validate([
            'description' => [
                'required',
                'string',
                'max:255',
                'not_regex:/[\'";=]|--/'
            ],
            'status' => 'required|in:In progress,Done',
            'date' => 'required|date',
        ]);

        $task->update([
            'description' => $validatedData['description'],
            'status' => $validatedData['status'],
            'date' => $validatedData['date'],
        ]);

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return RedirectResponse
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->back();
    }
}
