<?php

use App\Models\Intern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create the tables and run any seeds if needed
    $this->artisan('migrate');
});

test('intern can view login page', function () {
    get(route('intern.login'))
        ->assertStatus(200)
        ->assertSee('Login');
});

test('intern can login with correct credentials', function () {
    $intern = Intern::factory()->create([
        'email' => 'correct@example.com',
        'password' => bcrypt('password123'),
    ]);

    post(route('intern.login'), [
        'email' => 'correct@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ])
        ->assertRedirect(route('intern.dashboard'));

    $this->assertAuthenticated('intern');
});

test('intern cannot login with incorrect password', function () {
    $intern = Intern::factory()->create([
        'email' => 'wrong@example.com',
        'password' => bcrypt('password123'),
    ]);

    post(route('intern.login'), [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword',
        '_token' => csrf_token(),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['email' => 'Invalid credentials']);

    $this->assertGuest('intern');
});

test('intern cannot login with email that does not exist', function () {
    post(route('intern.login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['email' => 'Invalid credentials']);

    $this->assertGuest('intern');
});

test('intern can view registration page', function () {
    get(route('intern.register'))
        ->assertStatus(200)
        ->assertSee('Intern Registration');
});

test('intern can register with valid data', function () {
    $response = post(route('intern.register'), [
        'name' => 'Test Intern',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        '_token' => csrf_token(),
    ]);

    $response->assertRedirect(route('intern.dashboard'));

    // Check that the intern was created in the database
    $this->assertDatabaseHas('interns', [
        'name' => 'Test Intern',
        'email' => 'test@example.com',
    ]);

    // Check that the intern was automatically logged in
    $this->assertAuthenticated('intern');
});

test('intern cannot register with existing email', function () {
    // Create an intern first
    Intern::factory()->create([
        'email' => 'existing@example.com',
    ]);

    // Try to register with the same email
    $response = post(route('intern.register'), [
        'name' => 'Another Intern',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        '_token' => csrf_token(),
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest('intern');
});

test('intern cannot register with invalid data', function () {
    // Test with missing name
    $response = post(route('intern.register'), [
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['name']);

    // Test with invalid email
    $response = post(route('intern.register'), [
        'name' => 'Test Intern',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['email']);

    // Test with short password
    $response = post(route('intern.register'), [
        'name' => 'Test Intern',
        'email' => 'test@example.com',
        'password' => '12345',
        'password_confirmation' => '12345',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['password']);

    // Test with non-matching password confirmation
    $response = post(route('intern.register'), [
        'name' => 'Test Intern',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['password']);

    // Verify no intern was created
    $this->assertDatabaseEmpty('interns');
    $this->assertGuest('intern');
});
