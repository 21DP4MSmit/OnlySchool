<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

// Routes BEZ role check
Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

// Routes ar AUTH check
Route::middleware('auth')->group(function () {

    // Routes AR ADMIN role check
    Route::middleware([RoleMiddleware::class.':admin'])->group(function () {

        Route::get('/table-manager', [TableController::class, 'index'])->name('table.manager');
        Route::post('/table-manager/insert', [TableController::class, 'insert'])->name('table.insert');
        Route::get('/table-manager/data/{table}', [TableController::class, 'fetchData'])->name('table.data');

        Route::get('/TeacherDashboard', function () {
            return Inertia::render('TeacherDashboard')->name('TeachBoard');
        });
        
        Route::get('/TeacherAbsences', function () {
            return Inertia::render('TeacherAbsences')->name('TeachAbsences');
        });
        
        Route::get('/TeacherClasses', function () {
            return Inertia::render('TeacherClasses')->name('TeachClasses');
        });

    });


    // Routes AR USER role check
    Route::middleware([RoleMiddleware::class.':user,admin'])->group(function () {

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::get('/dienasgramata', function () {
            return Inertia::render('Dienasgramata');
        })->name('dienasgramata');

        Route::get('/kavejumi', function () {
            return Inertia::render('Kavejumi');
        })->name('');
        
        Route::get('/atzimes', function () {
            return Inertia::render('Atzimes');
        })->name('');
        
        Route::resource('conversations', ConversationController::class);
        Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');      

        Route::post('/profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');


    });
});

require __DIR__.'/auth.php';