<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"]);
Route::post("/update-password", [AuthController::class, "updatePassword"]);
Route::post("/reset-password", [AuthController::class, "resetPassword"]);

Route::post("/create-update-user", [UserController::class, "createUpdateUser"])->middleware('api_token:STAFF');
Route::get("/get-users", [UserController::class, "getUsers"])->middleware('api_token:STAFF');
Route::get("/get-user", [UserController::class, "getUser"]);

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/get-tasks', [TasksController::class, 'getTasks'])->middleware('api_token:STAFF');
Route::get('/dispatch/get-tasks', [TasksController::class, 'getDispatchTasks'])->middleware('api_token:DISPATCH');
Route::get('/get-task', [TasksController::class, 'getTask'])->middleware('api_token:');
Route::post('/create-update-task', [TasksController::class, 'createUpdateTask'])->middleware('api_token:STAFF');
Route::post('/complete-task', [TasksController::class, 'completeTask'])->middleware('api_token:DISPATCH');
Route::post('/claim-task', [TasksController::class, 'claimTask'])->middleware('api_token:DISPATCH');
Route::post('/cancel-task', [TasksController::class, 'cancelTask'])->middleware('api_token:STAFF');
