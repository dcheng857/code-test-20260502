<?php

use App\Http\Controllers\Api\TodoItemController;
use Illuminate\Support\Facades\Route;

Route::apiResource('todo-items', TodoItemController::class);
