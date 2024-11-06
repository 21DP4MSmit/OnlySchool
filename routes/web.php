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

Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::middleware('auth')->group(function () {

    Route::middleware([RoleMiddleware::class.':admin'])->group(function () {

        Route::get('/table-manager', [TableController::class, 'index'])->name('table.manager');
        Route::post('/table-manager/insert', [TableController::class, 'insert'])->name('table.insert');
        Route::get('/table-manager/data/{table}', [TableController::class, 'fetchData'])->name('table.data');
        Route::post('/table-manager/update/{table}/{id}', [TableController::class, 'update'])->name('table.update');
        Route::post('/table-manager/insert-user', [TableController::class, 'insertUser'])->name('table.insert.user');
        Route::get('/users-by-class/{class_id}', [UserController::class, 'getUsersByClass']);
        Route::get('/user-by-class/{classId}', [UserController::class, 'getUserByClass']);
        Route::post('/save-absences', [\App\Http\Controllers\AbsenceController::class, 'saveAbsences']);



        Route::get('/TeacherDashboard', function () {
            return Inertia::render('TeacherDashboard');
        })->name('TeachBoard');
        
        Route::get('/TeacherAbsences', [DienasgramataController::class, 'teacherAbsences'])->name('teacher.absences.index');

        
        Route::get('/TeacherClasses', [DienasgramataController::class, 'teacherClasses'])->name('teacher.classes.index');

        Route::get('/TeacherDashboard', [DienasgramataController::class, 'todayClasses'])->name('teacher.dashboard');


    });

    Route::middleware([RoleMiddleware::class.':user,admin'])->group(function () {

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::get('/dienasgramata', [DienasgramataController::class, 'index'])->name('dienasgramata.index');

        Route::get('/kavejumi', function () {
            return Inertia::render('Kavejumi');
        });

        Route::get('/atzimes', function () {
            return Inertia::render('Atzimes');
        });

        Route::resource('conversations', ConversationController::class);
        Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/conversations/leave', [ConversationController::class, 'leave'])->name('conversations.leave');
        Route::post('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

        Route::post('/conversations/{conversation}/remove-participant', [ConversationController::class, 'removeParticipant'])->name('conversations.remove-participant');
        Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');      

        Route::post('/profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
        Route::get('/profile-picture', [ProfileController::class, 'getProfilePicture'])->name('profile.picture');

        Route::get('/api/timetable/today', [StudentDashboardController::class, 'getTodayTimetable'])->name('timetable.today');

        Route::get('/api/grades/recent', [StudentDashboardController::class, 'getRecentGrades'])->name('grades.recent');

        Route::get('/api/absences/monthly', [StudentDashboardController::class, 'getMonthlyAbsences'])->name('absences.monthly');

    });

});

require __DIR__.'/auth.php';
