<?php

namespace App\Providers;

use App\Models\LessonPlan;
use App\Policies\LessonPlanPolicy;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\GeminiService::class);
        $this->app->singleton(\App\Services\GoogleDocsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Tool::observe(\App\Observers\ToolObserver::class);

        // ── Policies ─────────────────────────────────────────────
        Gate::policy(LessonPlan::class, LessonPlanPolicy::class);

        view()->composer('*', function ($view) {
            $categories = \Illuminate\Support\Facades\Cache::rememberForever(CacheKeys::WELCOME_CATEGORIES, function () {
                return \App\Models\Category::withCount('approvedTools')
                    ->orderBy('sort_order')
                    ->get();
            });

            if ($categories instanceof \__PHP_Incomplete_Class || !is_iterable($categories)) {
                \Illuminate\Support\Facades\Cache::forget(CacheKeys::WELCOME_CATEGORIES);
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
