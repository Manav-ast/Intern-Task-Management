<?php

use App\Models\Intern;
use App\Models\Task;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->artisan('migrate');

    // Create an admin
    $this->admin = Admin::create([
        'name' => 'Test Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123')
    ]);

    // Create an intern
    $this->intern = Intern::factory()->create([
        'email' => 'intern@example.com',
        'password' => bcrypt('password123')
    ]);

    // Create a task
    $this->task = Task::create([
        'title' => 'Test Task',
        'description' => 'This is a test task',
        'status' => 'pending',
        'created_by' => $this->admin->id,
        'due_date' => now()->addDays(7)
    ]);

    // Assign the task to the intern
    $this->task->interns()->attach($this->intern->id);
});

test('intern can view assigned tasks', function() {
    // Login as intern
    post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    // Access tasks index page
    $response = get(route('intern.tasks.index'));

    $response->assertStatus(200)
        ->assertSee('Test Task')
        ->assertSee('Pending');
});

test('intern can view individual task details', function() {
    // Login as intern
    post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    // Access task details page
    $response = get(route('intern.tasks.show', $this->task));

    $response->assertStatus(200)
        ->assertSee('Test Task')
        ->assertSee('This is a test task')
        ->assertSee('Pending');
});

test('intern cannot view tasks they are not assigned to', function() {
    // Create another intern and task
    $otherIntern = Intern::factory()->create();
    $otherTask = Task::create([
        'title' => 'Other Task',
        'description' => 'This is another test task',
        'status' => 'pending',
        'created_by' => $this->admin->id,
        'due_date' => now()->addDays(7)
    ]);
    $otherTask->interns()->attach($otherIntern->id);

    // Login as first intern
    $loginResponse = post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    $loginResponse->assertStatus(302); // Successful login should redirect    // Try to access other task with JSON request
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get(route('intern.tasks.show', $otherTask));

    $response->assertStatus(403)
        ->assertJson(['message' => 'You are not authorized to view this task.']);
});

test('intern can add comment to assigned task', function() {
    // Login as intern
    post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    // Add a comment
    $response = post(route('intern.tasks.comments.store', $this->task), [
        'message' => 'This is a test comment',
        '_token' => csrf_token(),
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'This is a test comment'
        ]);

    // Verify comment was saved
    $this->assertDatabaseHas('comments', [
        'message' => 'This is a test comment',
        'task_id' => $this->task->id,
        'commentable_id' => $this->intern->id,
        'commentable_type' => Intern::class
    ]);
});

test('intern cannot add comment to unassigned task', function() {
    // Create another task (not assigned to intern)
    $otherTask = Task::create([
        'title' => 'Other Task',
        'description' => 'This is another test task',
        'status' => 'pending',
        'created_by' => $this->admin->id,
        'due_date' => now()->addDays(7)
    ]);

    // Login as intern
    post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    // Try to add a comment
    $response = post(route('intern.tasks.comments.store', $otherTask), [
        'message' => 'This is a test comment',
        '_token' => csrf_token(),
    ]);

    $response->assertStatus(403);

    // Verify no comment was saved
    $this->assertDatabaseMissing('comments', [
        'message' => 'This is a test comment',
        'task_id' => $otherTask->id,
    ]);
});

test('intern sees validation error when submitting empty comment', function() {
    // Login as intern
    post(route('intern.login'), [
        'email' => 'intern@example.com',
        'password' => 'password123',
        '_token' => csrf_token(),
    ]);

    // Try to add an empty comment with proper headers
    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest'
    ])->post(route('intern.tasks.comments.store', $this->task), [
        'message' => ''
    ]);

    $response->dump() // Let's see what the response contains
        ->assertStatus(422)
        ->assertJsonValidationErrors(['message']);
});
