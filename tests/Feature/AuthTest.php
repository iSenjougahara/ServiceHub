<?php

use App\Models\User;
use App\Models\UserProfile;

test('guest can view login page', function () {
    $this->get('/login')->assertOk();
});

test('guest can view register page', function () {
    $this->get('/register')->assertOk();
});

test('guest cannot access home page', function () {
    $this->get('/')->assertRedirect('/login');
});

test('user can register with valid data', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'position' => 'manager',
        'department' => 'Operations',
        'phone' => '11999990000',
        'bio' => 'A test user.',
    ])->assertRedirect('/');

    expect(User::count())->toBe(1);
    expect(UserProfile::count())->toBe(1);

    $user = User::first();
    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->profile->position)->toBe('manager');
    expect($user->profile->department)->toBe('Operations');

    $this->assertAuthenticatedAs($user);
});

test('register validates required fields', function () {
    $this->post('/register', [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('register rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/register', [
        'name' => 'Duplicate',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

test('register rejects invalid position', function () {
    $this->post('/register', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'position' => 'ceo',
    ])->assertSessionHasErrors('position');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

test('login fails with wrong password', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});

test('authenticated user is redirected from login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/login')->assertRedirect('/');
});
