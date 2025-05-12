<?php

use App\Models\Admin;
use App\Models\Intern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get, post, put, delete};

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->withoutExceptionHandling();

    // Create an admin user
    $this->admin = Admin::create([
        'name' => 'Test Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
        'is_super_admin' => true
    ]);

    // Login as admin
    $response = post(route('admin.login'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $this->assertAuthenticated('admin');
});

test('admin can view list of interns', function() {
    // Create some test interns
    $interns = Intern::factory()->count(3)->create();

    $response = get(route('admin.interns.index'));

    $response->assertStatus(200);
    foreach ($interns as $intern) {
        $response->assertSee($intern->name)
                ->assertSee($intern->email);
    }
});

test('admin can create a new intern', function() {
    // Give admin the permission to create interns
    $this->admin->givePermissionTo('create-interns');

    $internData = [
        'name' => 'New Intern',
        'email' => 'newintern@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = post(route('admin.interns.store'), $internData);

    if ($response->status() !== 302) {
        dd($response->content()); // Debug any error messages
    }

    $response->assertStatus(302); // Redirect after successful creation
    $response->assertRedirect(route('admin.interns.index'));

    // Check if intern was created in database
    $this->assertDatabaseHas('interns', [
        'name' => 'New Intern',
        'email' => 'newintern@example.com',
    ]);
});

test('admin can update intern details', function() {
    // Give admin the permission to edit interns
    $this->admin->givePermissionTo('edit-interns');

    $intern = Intern::factory()->create();

    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $response = put(route('admin.interns.update', $intern), $updatedData);

    if ($response->status() !== 302) {
        dd($response->content()); // Debug any error messages
    }

    $response->assertStatus(302); // Redirect after successful update
    $response->assertRedirect(route('admin.interns.index'));

    // Check if intern was updated in database
    $this->assertDatabaseHas('interns', [
        'id' => $intern->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('admin can delete an intern', function() {
    // Give admin the permission to delete interns
    $this->admin->givePermissionTo('delete-interns');

    $intern = Intern::factory()->create();

    $response = delete(route('admin.interns.destroy', $intern));

    if ($response->status() !== 302) {
        dd($response->content()); // Debug any error messages
    }

    $response->assertStatus(302); // Redirect after successful deletion
    $response->assertRedirect(route('admin.interns.index'));

    // Check if intern was soft deleted (since we're using SoftDeletes)
    $this->assertSoftDeleted('interns', [
        'id' => $intern->id,
    ]);
});

test('admin cannot create intern with duplicate email', function() {
    // Create an intern first
    $existingIntern = Intern::factory()->create();

    // Try to create another intern with same email
    $internData = [
        'name' => 'New Intern',
        'email' => $existingIntern->email, // Using same email
        'password' => 'password123',
        'password_confirmation' => 'password123',
        '_token' => csrf_token(),
    ];

    $response = post(route('admin.interns.store'), $internData);

    $response->assertStatus(302)
            ->assertSessionHasErrors('email');
});

test('admin cannot create intern with invalid data', function() {
    $invalidData = [
        'name' => '', // Empty name
        'email' => 'not-an-email', // Invalid email format
        'password' => 'short', // Too short password
        'password_confirmation' => 'short',
        '_token' => csrf_token(),
    ];

    $response = post(route('admin.interns.store'), $invalidData);

    $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('admin cannot update intern with duplicate email', function() {
    // Create two interns
    $intern1 = Intern::factory()->create();
    $intern2 = Intern::factory()->create();

    // Try to update intern2 with intern1's email
    $response = put(route('admin.interns.update', $intern2), [
        'name' => 'Updated Name',
        'email' => $intern1->email,
        '_token' => csrf_token(),
    ]);

    $response->assertStatus(302)
            ->assertSessionHasErrors('email');
});
