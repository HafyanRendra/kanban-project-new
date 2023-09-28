<?php

namespace App\Http\Controllers;

use App\Models\Task;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // uncomment
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TaskFile;
use Illuminate\Http\Response;
use App\Http\Resources\TaskResource;



class TaskController extends Controller
{


    public function __construct()
    {
    }

    public function index()
    {
        $pageTitle = 'Task List'; // Ditambahkan
        $tasks = Task::all();
        //if (Gate::allows('viewAnyTask', Task::class)) {
        //    $tasks = Task::all();
        //} else {
        //    $tasks = Task::where('user_id', Auth::user()->id)->get();
        //}
        return response()->json([
            'code' => 200,
            'message' => 'Task successfully',
            'data' => TaskResource::collection($tasks),
        ]);
        // return view('tasks.index', [
        //     'pageTitle' => $pageTitle, //Ditambahkan
        //     'tasks' => $tasks,
        // ]);
    }

    public function create()
    {
        $pageTitle = 'Create Task';
        return view('tasks.create', ['pageTitle' => $pageTitle]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'due_date' => 'required',
                'status' => 'required',
                'file' => ['max:5000', 'mimes:pdf,jpeg,png'],
            ],
            [
                'file.max' => 'The file size exceed 5 mb',
                'file.mimes' => 'Must be a file of type: pdf,jpeg,png',
            ],

            $request->all()
        );

        DB::beginTransaction();
        try {
            $task = Task::create([
                'name' => $request->name,
                'detail' => $request->detail,
                'due_date' => $request->due_date,
                'status' => $request->status,
                'user_id' => Auth::user()->id,
            ]);

            $file = $request->file('file');
            if ($file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storePubliclyAs(
                    'tasks',
                    $file->hashName(),
                    'public'
                );

                TaskFile::create([
                    'task_id' => $task->id,
                    'filename' => $filename,
                    'path' => $path,
                ]);
            }
            $pageTitle = 'Task List'; // Ditambahkan
            $tasks = Task::all();
            DB::commit();
            return response()->json([

                'message' => 'Task created successfully',
            ]);
            // return redirect()->route('tasks.index');}
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([

                'message' => 'Task created unsuccessfully',
            ]);

            //     return redirect()
            //         ->route('tasks.create')
            //         ->with('error', $th->getMessage());
        }
    }

    public function edit($id)
    {
        // $pageTitle = 'Edit Task';
        // $task = Task::findOrFail($id);
        $task = Task::find($id);

        return response()->json([
            'data' => new TaskResource($task),
        ], Response::HTTP_OK);
        // Gate::authorize('update', $task); // Ditambahkan
        // if (Gate::denies('performAsTaskOwner', $task)) {
        //     Gate::authorize('updateAnyTask', Task::class);
        // }


        // return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $task = Task::findOrFail($id);
        //Gate::authorize('update', $task); // Ditambahkan
        // if (Gate::denies('performAsTaskOwner', $task)) {
        //     Gate::authorize('updateAnyTask', Task::class);
        // }

        $task->update([
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);
        // return redirect()->route('tasks.index');
        return response()->json([
            'code' => 200,
            'message' => 'Task update successfully',
        ]);
    }

    public function delete($id)
    {
        $pageTitle = 'Delete Task'; // Menyebutkan judul dari halaman yaitu "Delete Task"
        $task = Task::findOrFail($id); //  Memperoleh data task menggunakan $id


        //Gate::authorize('delete', $task); // Ditambahkan
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('deleteAnyTask', Task::class);
        }

        return view('tasks.delete', ['pageTitle' => $pageTitle, 'task' => $task]); // Menghasilkan nilai return berupa file view dengan halaman dan data task di atas 
    }

    public function destroy($id)
    {
        // $task = Task::findorFail($id);// Memperoleh task tertentu menggunakan $id

        //    //Gate::authorize('delete', $task); // Ditambahkan
        //    if (Gate::denies('performAsTaskOwner', $task)) {
        //     Gate::authorize('deleteAnyTask', Task::class);
        // }
        //    $task->delete();

        //     return redirect()->route('tasks.index');// Melakukan redirect menuju tasks.index
        //     }

        //     public function progress()
        // {

        //     $title = 'Task Progress';

        //     if (Gate::allows('viewAnyTask', Task::class)) {
        //         $tasks = Task::all();
        //     } else {
        //         $tasks = Task::where('user_id', Auth::user()->id)->get();
        //     }
        //     //$tasks = Task::all();

        //     $filteredTasks = $tasks->groupBy('status');


        //     $tasks = [
        //         Task::STATUS_NOT_STARTED => $filteredTasks->get(
        //             Task::STATUS_NOT_STARTED, []
        //         ),
        //         Task::STATUS_IN_PROGRESS => $filteredTasks->get(
        //             Task::STATUS_IN_PROGRESS, []
        //         ),
        //         Task::STATUS_IN_REVIEW => $filteredTasks->get(
        //             Task::STATUS_IN_REVIEW, []
        //         ),
        //         Task::STATUS_COMPLETED => $filteredTasks->get(
        //             Task::STATUS_COMPLETED, []
        //         ),
        //     ];

        //     //dd($tasks);
        //     return view('tasks.progress', [
        //         'pageTitle' => $title,
        //         'tasks' => $tasks,
        //     ]);

        $task = Task::find($id);
        // dd($task);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], Response::HTTP_NOT_FOUND);
        }

        foreach ($task->files as $file) {
            Storage::disk('public')->delete($file->path);
            $file->delete();
        }

        $task->delete();
        return response()->json([

            'message' => 'Tasks' . $task->name . 'Delete data successfully',

        ]);
    }

    public function move(int $id, Request $request)
    {
        $task = Task::findOrFail($id);
        // $task = Task::find($id);
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            'status' => $request->status,
        ]);
        // return response()->json([
        //     'code'=>200,
        //     'message'=> 'Task successfully',
        //     'data'=> TaskResource::collection($task),
        //    ]);
        return redirect()->route('tasks.progress');
    }


    public function updateFromTaskList($id)
    {
        $task = Task::find($id);
        //Gate::authorize('update', $task); // Ditambahkan
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            'status' => Task::STATUS_COMPLETED,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function updateStatusCardBlade($id)
    {
        $task = Task::find($id);
        //Gate::authorize('update', $task); // Ditambahkan
        if (Gate::denies('performAsTaskOwner', $task)) {
            Gate::authorize('updateAnyTask', Task::class);
        }
        $task->update([
            'status' => Task::STATUS_COMPLETED,
        ]);

        return redirect()->route('tasks.progress');
    }

    public function home()
    {
        // $tasks = Task::where('user_id', auth()->id())->get();
        $tasks = Task::get();

        $completed_count = $tasks
            ->where('status', Task::STATUS_COMPLETED)
            ->count();

        $uncompleted_count = $tasks
            ->whereNotIn('status', Task::STATUS_COMPLETED)
            ->count();

        return response()->json([
            'completed_count' => $completed_count,
            'uncompleted_count' => $uncompleted_count,
        ], Response::HTTP_OK);


        //     return view('home', [
        //         'completed_count' => $completed_count,
        //         'uncompleted_count' => $uncompleted_count,
        //     ]);
    }
}
