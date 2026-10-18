<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\Task;
use App\Policies\ProjectInvitationPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        RateLimiter::for('password-otp-send', function (Request $request): Limit {
            $email = strtolower(trim((string) $request->input('email', $request->session()->get('password_reset_otp_email', ''))));

            return Limit::perMinute(3)->by($request->ip().'|'.hash('sha256', $email));
        });

        RateLimiter::for('password-otp-verify', function (Request $request): Limit {
            $email = strtolower(trim((string) $request->session()->get('password_reset_otp_email', '')));

            return Limit::perMinute(6)->by($request->ip().'|'.hash('sha256', $email));
        });

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(ProjectInvitation::class, ProjectInvitationPolicy::class);

        View::composer('layouts.app', function (ViewInstance $view): void {
            $view->with(
                'unreadNotificationsCount',
                auth()->check() ? auth()->user()->unreadNotifications()->count() : 0,
            );
        });
    }
}
