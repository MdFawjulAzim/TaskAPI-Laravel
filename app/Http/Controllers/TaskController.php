<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        // dd(auth()->user());
        // dd(Auth::user()->id);
        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'title'     => 'required|string|max:100',
            'description'    => 'string|nullable',
            'status' => 'in:todo,in_progress,done',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $user_id = auth()->user()->id;
        // dd($user_id);
        $task = Task::create([
            'user_id' => $user_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Task Create Successfully!',
            'data' => $task
        ], 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        if ($user->id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. You can only read your own tasks.'
            ], 403);
        }
        // dd($user);
        $task = $user->tasks;
        // dd($task);


        return response()->json([
            'status' => true,
            'message' => 'Hello, ' . Auth::user()->name . ' .your task is show.',
            'data' => $task
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd('update');
        $task = Task::find($id);

        // dd($task);
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. You can only update your own tasks.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'string|max:100',
            'description' => 'nullable|string',
            'status'      => 'in:todo,in_progress,done',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Update only allowed fields
        $task->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Task updated successfully!',
            'data' => $task
        ]);
    }

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'status' => false,
                'message' => 'Task not found.'
            ], 404);
        }

        // Ensure the authenticated user owns this task
        if ($task->user_id !== Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. You can only delete your own tasks.'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'status' => true,
            'message' => 'Task deleted successfully!'
        ]);
    }
}
