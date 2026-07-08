<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// Uses the given trait in the current file
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(Tests\TestCase::class)->in('Unit');
uses(Tests\TestCase::class)->in('Feature');
uses(Tests\TestCase::class)->in('Browser');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

use App\Models\Role;
use App\Models\User;

function asUser(): User
{
    $user = User::factory()->create(['role' => Role::USER]);
    test()->actingAs($user);

    return $user;
}

function asAdmin(): User
{
    $user = User::factory()->create(['role' => Role::ADMIN]);
    test()->actingAs($user);

    return $user;
}

// The admin SPA's router guard checks sessionStorage (set only by a real login POST response),
// not the Laravel session cookie actingAs() injects — so Browser tests that need to land on a
// protected /admin/* page must log in via the real form. Use these instead of asAdmin()/asUser()
// whenever a Browser test needs to be on an authenticated page (asAdmin()/asUser() are still
// fine for server-side-only setup that doesn't depend on the SPA's own auth state).
function loginViaFormAndNavigate(User $user, string $path)
{
    return visit('/admin/login')
        ->type('username', $user->email)
        ->type('password', 'password')
        ->click('Anmelden')
        ->navigate($path);
}

function visitAsAdmin(string $path)
{
    return loginViaFormAndNavigate(User::factory()->create(['role' => Role::ADMIN]), $path);
}

function visitAsUser(string $path)
{
    return loginViaFormAndNavigate(User::factory()->create(['role' => Role::USER]), $path);
}
