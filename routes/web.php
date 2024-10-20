<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dienasgramata', function () {
    return Inertia::render('Dienasgramata');
})->middleware(['auth', 'verified']);
Route::get('/kavejumi', function () {
    return Inertia::render('Kavejumi');
})->middleware(['auth', 'verified']);
Route::get('/atzimes', function () {
    return Inertia::render('Atzimes');
})->middleware(['auth', 'verified']);
Route::get('/vestules', function () {
    return Inertia::render('Vestules');
})->middleware(['auth', 'verified']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/table-manager', [TableController::class, 'index'])->name('table.manager');
    Route::post('/table-manager/insert', [TableController::class, 'insert'])->name('table.insert');
    Route::get('/table-manager/data/{table}', [TableController::class, 'fetchData'])->name('table.data');
});


Route::get('/TeacherDashboard', function () {
    return Inertia::render('TeacherDashboard')->name('TeachBoard');
});

Route::get('/TeacherAbsences', function () {
    return Inertia::render('TeacherAbsences');
});

Route::get('/TeacherClasses', function () {
    return Inertia::render('TeacherClasses');
});


require __DIR__.'/auth.php';
