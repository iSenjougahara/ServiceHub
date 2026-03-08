<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserProfile;

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Test Corp',
        'cnpj' => '12345678000100',
        'email' => 'test@corp.com',
        'phone' => '11999990000',
        'address' => 'Test Street, 100',
    ]);

    $this->project = Project::create([
        'company_id' => $this->company->id,
        'name' => 'Test Project',
        'email' => 'project@corp.com',
        'status' => 'active',
        'start_date' => '2025-01-01',
    ]);

    $this->technician = User::factory()->create();
    $techProfile = UserProfile::create([
        'user_id' => $this->technician->id,
        'position' => 'technician',
        'department' => 'IT Support',
    ]);
    $techProfile->projects()->attach($this->project->id);

    $this->regularUser = User::factory()->create();
    UserProfile::create([
        'user_id' => $this->regularUser->id,
        'position' => 'manager',
        'department' => 'Operations',
    ]);

    $this->ticket = Ticket::create([
        'user_id' => $this->regularUser->id,
        'project_id' => $this->project->id,
        'title' => 'Test Ticket',
        'priority' => 'medium',
        'status' => 'open',
    ]);
});

// --- Take Ticket ---

test('technician can take an open ticket', function () {
    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/take")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('in_progress');
});

test('technician can take an in_progress ticket', function () {
    $this->ticket->update(['status' => 'in_progress']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/take")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('in_progress');
});

test('technician cannot take a resolved ticket', function () {
    $this->ticket->update(['status' => 'resolved']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/take")
        ->assertStatus(422);
});

test('technician cannot take a closed ticket', function () {
    $this->ticket->update(['status' => 'closed']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/take")
        ->assertStatus(422);
});

test('non-technician cannot take a ticket', function () {
    $this->actingAs($this->regularUser)
        ->post("/tickets/{$this->ticket->id}/take")
        ->assertForbidden();

    expect($this->ticket->fresh()->status)->toBe('open');
});

// --- Resolve Ticket ---

test('technician can resolve an open ticket', function () {
    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/resolve")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('resolved');
});

test('technician can resolve an in_progress ticket', function () {
    $this->ticket->update(['status' => 'in_progress']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/resolve")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('resolved');
});

test('technician cannot resolve a closed ticket', function () {
    $this->ticket->update(['status' => 'closed']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/resolve")
        ->assertStatus(422);
});

test('non-technician cannot resolve a ticket', function () {
    $this->actingAs($this->regularUser)
        ->post("/tickets/{$this->ticket->id}/resolve")
        ->assertForbidden();

    expect($this->ticket->fresh()->status)->toBe('open');
});

// --- Close Ticket ---

test('technician can close an open ticket', function () {
    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/close")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('closed');
});

test('technician can close an in_progress ticket', function () {
    $this->ticket->update(['status' => 'in_progress']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/close")
        ->assertRedirect('/');

    expect($this->ticket->fresh()->status)->toBe('closed');
});

test('technician cannot close a resolved ticket', function () {
    $this->ticket->update(['status' => 'resolved']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/close")
        ->assertStatus(422);
});

test('technician cannot close an already closed ticket', function () {
    $this->ticket->update(['status' => 'closed']);

    $this->actingAs($this->technician)
        ->post("/tickets/{$this->ticket->id}/close")
        ->assertStatus(422);
});

test('non-technician cannot close a ticket', function () {
    $this->actingAs($this->regularUser)
        ->post("/tickets/{$this->ticket->id}/close")
        ->assertForbidden();

    expect($this->ticket->fresh()->status)->toBe('open');
});
