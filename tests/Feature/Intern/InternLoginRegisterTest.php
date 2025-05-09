<?php

namespace Tests\Feature\Intern;

use App\Models\Intern;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InternLoginRegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test intern login with valid credentials.
     */
    public function test_intern_can_login_with_valid_credentials()
    {
        // Create an intern
        $intern = Intern::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login
        $response = $this->post('/intern/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert the user is authenticated
        $this->assertAuthenticatedAs($intern, 'intern');

        // Assert redirection to dashboard
        $response->assertRedirect(route('intern.dashboard'));
    }

    /**
     * Test intern login with invalid credentials.
     */
    public function test_intern_cannot_login_with_invalid_credentials()
    {
        // Create an intern
        Intern::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Attempt to login with wrong password
        $response = $this->post('/intern/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert the user is not authenticated
        $this->assertGuest('intern');

        // Assert session has errors
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test intern registration with valid data.
     */
    public function test_intern_can_register_with_valid_data()
    {
        $response = $this->post('/intern/register', [
            'name' => 'Test Intern',
            'email' => 'new-intern@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert the database has the user
        $this->assertDatabaseHas('interns', [
            'name' => 'Test Intern',
            'email' => 'new-intern@example.com',
        ]);

        // Assert the user is authenticated
        $this->assertAuthenticated('intern');

        // Assert redirection to dashboard
        $response->assertRedirect(route('intern.dashboard'));
    }

    /**
     * Test intern registration with invalid data.
     */
    public function test_intern_cannot_register_with_invalid_data()
    {
        // Create an existing intern to test unique email validation
        Intern::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Attempt to register with existing email
        $response = $this->post('/intern/register', [
            'name' => 'Test Intern',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('email');

        // Assert the user is not authenticated
        $this->assertGuest('intern');
    }

    /**
     * Test intern registration with mismatched passwords.
     */
    public function test_intern_cannot_register_with_mismatched_passwords()
    {
        $response = $this->post('/intern/register', [
            'name' => 'Test Intern',
            'email' => 'new-intern@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        // Assert validation errors
        $response->assertSessionHasErrors('password');

        // Assert the database doesn't have the user
        $this->assertDatabaseMissing('interns', [
            'email' => 'new-intern@example.com',
        ]);

        // Assert the user is not authenticated
        $this->assertGuest('intern');
    }

    /**
     * Test intern logout functionality.
     */
    public function test_intern_can_logout()
    {
        // Create an intern with known credentials
        $intern = Intern::factory()->create([
            'email' => 'logout-test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Login the intern
        $this->post('/intern/login', [
            'email' => 'logout-test@example.com',
            'password' => 'password123',
        ]);

        // Verify we're logged in
        $this->assertAuthenticated('intern');

        // Attempt to logout
        $response = $this->post(route('intern.logout'));

        // Assert the user is not authenticated
        $this->assertGuest('intern');

        // Assert redirection to login page
        $response->assertRedirect(route('intern.login'));
    }
}
