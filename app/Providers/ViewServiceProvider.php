<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer([
            'layouts.app',
            'layouts.client-portal',
        ], function ($view) {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();
            $currentPlan = $user->plan_type;

            $view->with([
                'authUser' => $user,
                'currentPlan' => $currentPlan,
                'currentPlanLabel' => $currentPlan->label(),
                'currentPlanColor' => $currentPlan->color(),
                'unreadNotificationsCount' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
