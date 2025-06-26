<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class TaskController extends Controller
{
    //Show tasks by date
    public function index(Request $request)
{
    try {
        $date = $request->query('date');
        
        $tasks = Task::when($date, function($query) use ($date) {
                return $query->whereDate('date', $date);
            })
            ->with(['participants' => function($query) {
                $query->select(['users.id', 'users.name', 'users.email']);
            }])
            ->where('user_id', Auth::id())
            ->select(['id', 'name', 'description', 'date', 'start_time', 'end_time', 'status', 'category'])
            ->withTrashed()
            ->get();
            
        return response()->json([
            'success' => true,
            'taskData' => $tasks,
            'message' => $tasks->isEmpty() ? 'No tasks found' : 'Tasks retrieved successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch tasks',
            'error' => $e->getMessage()
        ], 500);
    }
}

    //Creating tasks
    public function create(Request $request){
        $user = User::find(Auth::id());
        $request->validate([
            'name' => 'required|string',
            'date' => 'required',
            'start' => 'required', 
            'end' => 'required', 
            'status' => 'nullable', 
            'description' => 'required|string',
            'category' => 'required|string',
            'participants' => 'array',
            'participants.*' => 'exists:users,id',
        ]);

        $task = Task::Create([
            'user_id' => $user->id,
            'name' => $request->name,
            'date' => $request->date,
            'start_time' => $request->start,
            'end_time' => $request->end,
            'status' => $request->status ?? 'pending',
            'description' => $request->description,
            'category' => $request->category,
        ]);
        
        if ($request->has('participants')) {
            $task->participants()->attach($request->participants);
        }

        if (!empty($request->participants)) {
        $users = User::whereIn('id', $request->participants)->get();
        foreach ($users as $participant) {
            if ($participant->expo_token) {
                Http::post('https://exp.host/--/api/v2/push/send', [
                    'to' => $participant->expo_token,
                    'title' => 'New Task Assigned!',
                    'body' => 'You have been assigned a new task: ' . $request->name,
                    'sound' => 'default',
                ]);
            }
        }
        }
        
        if ($task) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task->load('participants')
            ], 201);

        }
        return response()->json([
                'success' => false,
                'message' => 'Task creation failed'
            ], 500);
    }

    //Edit Tasks
    public function edit(Request $request){
        $request->validate([
            'name' => 'required|string',
            'date' => 'required',
            'description' => 'required|string',
            'category' => 'required|string',
        ]);

        $task = Task::findOrFail($request->id);

        $updateTask = $task->update([
            'name' => $request->name,
            'date' => $request->date,
            'description' => $request->description,
            'category' => $request->category,
        ]);

        if ($updateTask) {
           return response()->json([
                'success' => true,
                'message' => 'Task Updated successfully',
                'data' => $updateTask
            ], 201);
            }
        return response()->json([
                'success' => false,
                'message' => 'Task updation failed'
            ], 500);
    }

    // Mark task as completed or undo
    public function complete(Task $task)
    {
        $task->status = $task->status === 'completed' ? 'pending' : 'completed';
        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully',
            'status' => $task->status,
        ]);
    }

    // Soft delete or mark as deleted
    public function delete(Task $task)
    {
        $task->status = "canceled";
        $task->save();
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    //Search Particpants
    public function searchUsers(Request $request)
    {
        $query = User::query();
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
        }
        
        return response()->json(
            $query->limit(10)->get(['id', 'name', 'email'])
        );
    }

    // Add participant to task
    public function addParticipant(Task $task, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $task->participants()->syncWithoutDetaching([$request->user_id]);

        return response()->json([
            'success' => true,
            'message' => 'Participant added successfully'
        ]);
    }

    // Remove participant from task
    public function removeParticipant(Task $task, Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $task->participants()->detach($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Participant removed successfully'
        ]);
    }
}
