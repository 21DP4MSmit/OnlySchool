<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\DienasgramataController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Routes WITHOUT role check
Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

// Routes WITH AUTH check
Route::middleware('auth')->group(function () {

    // Routes WITH ADMIN role check
    Route::middleware([RoleMiddleware::class.':admin'])->group(function () {

        // Table Manager Routes
        Route::get('/table-manager', [TableController::class, 'index'])->name('table.manager');
        Route::post('/table-manager/insert', [TableController::class, 'insert'])->name('table.insert');
        Route::get('/table-manager/data/{table}', [TableController::class, 'fetchData'])->name('table.data');
        Route::post('/table-manager/update/{table}/{id}', [TableController::class, 'update'])->name('table.update');
        Route::post('/table-manager/insert-user', [TableController::class, 'insertUser'])->name('table.insert.user');
        Route::get('/users-by-class/{class_id}', [UserController::class, 'getUsersByClass']);
        Route::post('/save-absences', [\App\Http\Controllers\AbsenceController::class, 'saveAbsences']);




        // Routes for Teacher Dashboard, Absences, and Classes
        Route::get('/TeacherDashboard', function () {
            return Inertia::render('TeacherDashboard');
        })->name('TeachBoard');
        
        Route::get('/TeacherAbsences', [DienasgramataController::class, 'teacherAbsences'])->name('teacher.absences.index');

        
        Route::get('/TeacherClasses', function () {
            return Inertia::render('TeacherClasses');
        })->name('TeachClasses');

    });

    // Routes WITH USER or ADMIN role check
    Route::middleware([RoleMiddleware::class.':user,admin'])->group(function () {

        // Dashboard Route
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        // Route for Dienasgramata
        Route::get('/dienasgramata', [DienasgramataController::class, 'index'])->name('dienasgramata.index');

        // Route for Kavējumi
        Route::get('/kavejumi', function () {
            return Inertia::render('Kavejumi');
        });

        // Route for Atzimes
        Route::get('/atzimes', function () {
            return Inertia::render('Atzimes');
        });

        // Conversations Routes
        Route::resource('conversations', ConversationController::class);
        Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/conversations/leave', [ConversationController::class, 'leave'])->name('conversations.leave');
        Route::post('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

        // Profile Routes
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');      

        // Profile Picture Routes
        Route::post('/profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
        Route::get('/profile-picture', [ProfileController::class, 'getProfilePicture'])->name('profile.picture');

        // Route to get today's timetable
        Route::get('/api/timetable/today', [StudentDashboardController::class, 'getTodayTimetable'])->name('timetable.today');

        // Route to get recent grades
        Route::get('/api/grades/recent', [StudentDashboardController::class, 'getRecentGrades'])->name('grades.recent');

        // Route to get student's absences for the current month
        Route::get('/api/absences/monthly', [StudentDashboardController::class, 'getMonthlyAbsences'])->name('absences.monthly');

    });

});

// Authentication Routes
require __DIR__.'/auth.php';
