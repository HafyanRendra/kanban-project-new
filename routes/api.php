<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\RoleController;
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


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('auth/signup', [AuthController::class, 'signup']);


Route::get('home',[TaskController::class, 'home'])->middleware('auth:sanctum');
Route::get('tasks',[TaskController::class, 'index'])->middleware('auth:sanctum');
Route::get('tasks/show/{id}',[TaskController::class, 'edit']);
Route::post('tasks/{id}/{edit',[TaskController::class, 'update'])->middleware('auth:sanctum');;
Route::post('/tasks/store',[TaskController::class,'store'])->middleware('auth:sanctum');
Route::delete('/tasks/{id}/destroy',[TaskController::class,'destroy']); 
// Route::patch('{id}/move','move',[TaskController::class,'move']); 

Route::get( 'roles/index',[RoleController::class,'index']); 
