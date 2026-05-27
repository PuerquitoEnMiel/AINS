<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \App\Models\Tool::observe(\App\Observers\ToolObserver::class);

        view()->composer('*', function ($view) {
            $categories = \Illuminate\Support\Facades\Cache::rememberForever('welcome_categories', function () {
                return \App\Models\Category::withCount('approvedTools')
                    ->orderBy('sort_order')
                    ->get();
            });

            if ($categories instanceof \__PHP_Incomplete_Class || !is_iterable($categories)) {
                \Illuminate\Support\Facades\Cache::forget('welcome_categories');
                $categories = \App\Models\Category::withCount('approvedTools')
                    ->orderBy('sort_order')
                    ->get();
            }

            $canSeeDetection = \Illuminate\Support\Facades\Auth::check() && 
                (\Illuminate\Support\Facades\Auth::user()->isTeacher() || \Illuminate\Support\Facades\Auth::user()->isAdmin());
            if (!$canSeeDetection) {
                $categories = $categories->filter(fn($c) => $c->slug !== 'ai-detection')->values();
            }

            $view->with('globalCategories', $categories);
        });
    }
}
