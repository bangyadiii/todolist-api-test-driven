<?php

use App\Http\Controllers\API\Auth\RegisterUserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::as("auth.")->prefix("auth")->group(function () {
    Route::post("/register", [RegisterUserController::class, "register"])->name("register");
    Route::get("/",  function () {
        return response("helo");
    });
});
