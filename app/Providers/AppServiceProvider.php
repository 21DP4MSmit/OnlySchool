<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Conversation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prefetch with concurrency setting
        Vite::prefetch(concurrency: 3);

        // Share the unread message count globally for authenticated users
        Inertia::share([
            'unreadMessageCount' => function () {
                if (auth()->check()) {
                    return Conversation::whereHas('messages', function ($query) {
                        $query->where('is_read', false)
                              ->where('user_id', '!=', auth()->id());
                    })->count();
                }
                return 0;
            },
        ]);
    }
}
