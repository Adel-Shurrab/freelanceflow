<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Enums\NotificationChannel;
use App\Enums\PlanType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'role' => UserRole::Freelancer,
            'plan_type' => PlanType::Free,
            'timezone' => config('app.timezone', 'UTC'),
            'language' => 'en',
            'notification_preferences' => NotificationChannel::defaultPreferences(),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Welcome to FreelanceFlow.')
        ;
    }
}
