<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create the tables and run any seeds if needed
    $this->artisan('migrate');
});

test('admin can view login page', function () {
    get(route('admin.login'))
        ->assertStatus(200)
        ->assertSee('Login');
});

test('admin can login with correct credentials', function () {
    $admin = Admin::create([
        'name' => 'Test Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
    ]);

    post(route('admin.login'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated('admin');
});

test('admin cannot login with incorrect password', function () {
    $admin = Admin::create([
        'name' => 'Test Admin',
        'email' => 'wrong@example.com',
        'password' => bcrypt('password123'),
    ]);

    post(route('admin.login'), [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword',
        '_token' => csrf_token(),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['email' => 'Invalid credentials']);

    $this->assertGuest('admin');
});

test('admin cannot login with email that does not exist', function () {
    post(route('admin.login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['email' => 'Invalid credentials']);

    $this->assertGuest('admin');
});
