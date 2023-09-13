<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('home',[TaskController::class, 'home'])->middleware('auth:sanctum');
Route::get('tasks',[TaskController::class, 'index']);
Route::get('tasks/show/{id}',[TaskController::class, 'edit']);
Route::post('tasks/{id}/{edit',[TaskController::class, 'update']);
Route::post('/tasks/store',[TaskController::class,'store']);
Route::delete('/tasks/{id}/destroy',[TaskController::class,'destroy']); 
// Route::patch('{id}/move','move',[TaskController::class,'move']); 