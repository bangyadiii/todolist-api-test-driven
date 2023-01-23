<?php

use App\Http\Controllers\API\TodoListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/todo-list', [TodoListController::class, "index"])->name("api.todolist");
Route::get('/todo-list/{id}', [TodoListController::class, "show"])->name("api.todolist.show");
Route::post('/todo-list', [TodoListController::class, "store"])->name("api.todolist.store");
Route::delete('/todo-list/{id}', [TodoListController::class, "destroy"])->name("api.todolist.delete");
Route::put('/todo-list/{id}', [TodoListController::class, "update"])->name("api.todolist.update");
