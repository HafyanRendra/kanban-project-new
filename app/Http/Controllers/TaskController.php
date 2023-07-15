<?php

namespace App\Http\Controllers;

use App\Models\Task;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    

    public function __construct()
    {
        
    }

    public function index()
    {
    $pageTitle = 'Task List'; // Ditambahkan
    $tasks = Task::all();
    return view('tasks.index', [
        'pageTitle' => $pageTitle, //Ditambahkan
        'tasks' => $tasks,
    ]);
    }

    public function create(){
        $pageTitle = 'Add task';
        return view('tasks.create', ['pageTitle' => $pageTitle]);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'due_date' => 'required',
                'status' => 'required',
            ],
            $request->all()
        );
        
        Task::create([
            'name' => $request->name,
            'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);

        return redirect()->route('tasks.index');
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Task';
        $task = Task::find($id);

        return view('tasks.edit', ['pageTitle' => $pageTitle, 'task' => $task]);
    }

    public function update(Request $request, $id)
    {
       // dd($request->all());
        $task = Task::find($id);
        $task->update([
            'name' => $request->name,
           'detail' => $request->detail,
            'due_date' => $request->due_date,
            'status' => $request->status,
        ]);
        return redirect()->route('tasks.index');
    }

    public function delete($id)
    {
    $pageTitle = 'Delete Task'; // Menyebutkan judul dari halaman yaitu "Delete Task"
    $task = Task::find($id); //  Memperoleh data task menggunakan $id
    return view('tasks.delete', ['pageTitle' => $pageTitle, 'task' => $task]);// Menghasilkan nilai return berupa file view dengan halaman dan data task di atas 
    }

    public function destroy($id)
    {
    $task = Task::find($id);// Memperoleh task tertentu menggunakan $id
    $task->delete();
    return redirect()->route('tasks.index');// Melakukan redirect menuju tasks.index
    }

    public function progress()
{
    
    $title = 'Task Progress';

    $tasks = Task::all();

    $filteredTasks = $tasks->groupBy('status');
    
   
    $tasks = [
        Task::STATUS_NOT_STARTED => $filteredTasks->get(
            Task::STATUS_NOT_STARTED, []
        ),
        Task::STATUS_IN_PROGRESS => $filteredTasks->get(
            Task::STATUS_IN_PROGRESS, []
        ),
        Task::STATUS_IN_REVIEW => $filteredTasks->get(
            Task::STATUS_IN_REVIEW, []
        ),
        Task::STATUS_COMPLETED => $filteredTasks->get(
            Task::STATUS_COMPLETED, []
        ),
    ];

    return view('tasks.progress', [
        'pageTitle' => $title,
        'tasks' => $tasks,
    ]);
}




    

    
}
