<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskFile;
use illuminate\support\facades\storage;

class TaskFileController extends Controller
{
    public function store($task_id, Request $request)
{
    $task = Task::findOrFail($task_id);

    $request->validate(
        [
            'file' => ['required', 'mimes:pdf,jpeg,png', 'max:5000'],
        ],
        [
            'file.max' => 'The file size exceed 5 mb',
            'file.mimes' => 'Must be a file of type: pdf,jpeg,png',
        ],
        $request->all()
    );

    $file = $request->file('file');
    $filename = $file->getClientOriginalName();
    $path = $file->storePubliclyAs('tasks', $file->hashName(), 'public');

    TaskFile::create([
        'task_id' => $task->id,
        'filename' => $filename,
        'path' => $path,
    ]);

    return redirect()->route('tasks.edit', ['id' => $task->id]);
}

public function destroy($task_id, $id)
{
    $file = TaskFile::findOrFail($id);

    Storage::disk('public')->delete($file->path);
    $file->delete();
    return redirect()->route('tasks.edit', ['id' => $task_id]);
}

public function show($task_id, $id)
{
    $file = TaskFile::findOrFail($id);
    $filePath = storage_path('app/public/' . $file->path);

    if (file_exists($filePath)) {
        return response()->file($filePath);
    } else {
        abort(404);
    }
}

}
