<?php

use App\Models\Intern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('migrate');

    // Create an intern
    $this->intern = Intern::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'password' => bcrypt('password123')
    ]);

    // Login as intern
    post(route('intern.login'), [
        'email' => 'original@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);
});

test('intern can view their profile edit page', function () {
    get(route('intern.profile.edit'))
        ->assertStatus(200)
        ->assertSee('Edit Profile')
        ->assertSee('Original Name')
        ->assertSee('original@example.com');
});

test('intern can update name and email', function () {
    $response = put(route('intern.profile.update'), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        '_token' => csrf_token(),
    ]);

    $response->assertRedirect(route('intern.dashboard'))
             ->assertSessionHas('success');

    // Verify the database was updated
    $this->assertDatabaseHas('interns', [
        'id' => $this->intern->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com'
    ]);
});

test('intern can update password', function () {
    $response = put(route('intern.profile.update'), [
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        '_token' => csrf_token(),
    ]);

    $response->assertRedirect(route('intern.dashboard'))
             ->assertSessionHas('success');

    // Try to login with new password
    post(route('intern.logout'));

    $loginResponse = post(route('intern.login'), [
        'email' => 'original@example.com',
        'password' => 'newpassword123',
        '_token' => csrf_token(),
    ]);

    $loginResponse->assertRedirect(route('intern.dashboard'));
    $this->assertAuthenticated('intern');
});

test('intern cannot use email that belongs to another intern', function () {
    // Create another intern
    $otherIntern = Intern::factory()->create([
        'email' => 'other@example.com'
    ]);

    $response = put(route('intern.profile.update'), [
        'name' => 'Original Name',
        'email' => 'other@example.com',
        '_token' => csrf_token(),
    ]);

    $response->assertSessionHasErrors(['email']);

    // Verify the database was not updated
    $this->assertDatabaseHas('interns', [
        'id' => $this->intern->id,
        'email' => 'original@example.com'
    ]);
});

test('intern cannot update with invalid data', function () {
    // Test with invalid email
    $response = put(route('intern.profile.update'), [
        'name' => 'Original Name',
        'email' => 'invalid-email',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['email']);

    // Test with empty name
    $response = put(route('intern.profile.update'), [
        'name' => '',
        'email' => 'original@example.com',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['name']);

    // Test with short password
    $response = put(route('intern.profile.update'), [
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'password' => '123',
        'password_confirmation' => '123',
        '_token' => csrf_token(),
    ]);
    $response->assertSessionHasErrors(['password']);
});

test('password confirmation must match', function () {
    $response = put(route('intern.profile.update'), [
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'differentpassword123',
        '_token' => csrf_token(),
    ]);

    $response->assertSessionHasErrors(['password']);

    // Verify the old password still works
    post(route('intern.logout'));

    $loginResponse = post(route('intern.login'), [
        'email' => 'original@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    $loginResponse->assertRedirect(route('intern.dashboard'));
    $this->assertAuthenticated('intern');
});

test('unauthenticated users cannot access profile', function () {
    // Logout first
    post(route('intern.logout'));

    // Try to access profile edit page
    get(route('intern.profile.edit'))
        ->assertRedirect(route('intern.login'));

    // Try to update profile
    put(route('intern.profile.update'), [
        'name' => 'Hacker',
        'email' => 'hacker@example.com',
        '_token' => csrf_token(),
    ])->assertRedirect(route('intern.login'));
});
