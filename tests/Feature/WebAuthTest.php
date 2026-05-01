<?php

declare(strict_types=1);

use App\Enums\PlanType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('allows guests to view the register page', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('Create your account')
    ;
});

it('allows guests to view the login page', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Login')
    ;
});

it('registers a new freelancer user and logs them in', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'New Freelancer',
        'email' => 'new@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();

    $user = User::query()
        ->where('email', 'new@example.com')
        ->firstOrFail()
    ;

    expect($user->role)->toBe(UserRole::Freelancer);
    expect($user->plan_type)->toBe(PlanType::Free);
    expect($user->last_login_at)->not->toBeNull();
});

it('does not register with invalid data', function () {
    $response = $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'different',
        ])
    ;

    $response->assertRedirect(route('register'));
    $response->assertSessionHasErrors(['name', 'email', 'password']);

    $this->assertGuest();
});

it('logs in an existing user', function () {
    $user = User::factory()->create([
        'email' => 'freelancer@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'freelancer@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);

    $user->refresh();

    expect($user->last_login_at)->not->toBeNull();
});

it('does not login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'freelancer@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'freelancer@example.com',
            'password' => 'wrong-password',
        ])
    ;

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('allows authenticated users to view dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee($user->email)
    ;
});

it('redirects guests away from dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'))
    ;
});

it('logs out authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('logout'))
    ;

    $response->assertRedirect(route('login'));

    $this->assertGuest();
});
