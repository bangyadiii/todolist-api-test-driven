<?php

use App\Http\Controllers\API\Auth\AuthenticatedController;
use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\Auth\RegisterUserController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\TodoListController;
use App\Models\TodoList;
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

Route::as("api.")->group(function () {
    Route::as("auth.")->prefix("auth")->group(function () {
        Route::post("/register", [RegisterUserController::class, "register"])->name("register");
        Route::post("/login", [AuthenticationController::class, "login"])->name("login");
    });

    Route::middleware("auth:sanctum")->group(function () {
        Route::apiResource("todolist", TodoListController::class);
        Route::apiResource("todolist.task", TaskController::class)->shallow()->except("show");
    });
});
