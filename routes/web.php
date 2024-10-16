<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/student-dashboard', function () {
    return Inertia::render('StudentDashboardPage');
})->name('student.dashboard');

Route::get('/dienasgramata', function () {
    return Inertia::render('Dienasgramata');
})->name('dienasgramata');

Route::get('/TeacherDashboard', function () {
    return Inertia::render('TeacherDashboard');
});

Route::get('/TeacherAbsences', function () {
    return Inertia::render('TeacherAbsences');
});

Route::get('/TeacherClasses', function () {
    return Inertia::render('TeacherClasses');
});


require __DIR__.'/auth.php';
