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
        // Share pending leave count with admin layout
        view()->composer('layouts.admin', function ($view) {
            $pendingLeaveCount = 0;
            if (auth()->check() && (auth()->user()->employee_role == 'hr' || auth()->user()->employee_role == 'owner')) {
                $roleToFetch = auth()->user()->employee_role === 'owner' ? 'hr' : 'employee';
                $pendingLeaveCount = \App\Models\LeavePermission::where('leave_status', 'pending')
                    ->whereHas('employee', function ($query) use ($roleToFetch) {
                        $query->where('employee_role', $roleToFetch);
                    })
                    ->count();
            }
            $view->with('pendingLeaveCount', $pendingLeaveCount);
        });
    }
}
