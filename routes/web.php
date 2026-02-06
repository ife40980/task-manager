<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

//this is the standard route for resources(index, create, store, edit, update, destroy)
Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
// Toggle status through AJAX (marks completed pending)
// I Used patch to follow restful semantics for a partial update
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggleStatus'])->name('tasks.toggle');

Route::resource('tasks', TaskController::class);
 