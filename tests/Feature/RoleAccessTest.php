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

    $this->projectA = Project::create([
        'company_id' => $this->company->id,
        'name' => 'Project A',
        'email' => 'a@corp.com',
        'status' => 'active',
        'start_date' => '2025-01-01',
    ]);

    $this->projectB = Project::create([
        'company_id' => $this->company->id,
        'name' => 'Project B',
        'email' => 'b@corp.com',
        'status' => 'active',
        'start_date' => '2025-02-01',
    ]);

    // Technician assigned to Project A only
    $this->technician = User::factory()->create();
    $techProfile = UserProfile::create([
        'user_id' => $this->technician->id,
        'position' => 'technician',
        'department' => 'IT Support',
    ]);
    $techProfile->projects()->attach($this->projectA->id);

    // Regular user
    $this->regularUser = User::factory()->create();
    UserProfile::create([
        'user_id' => $this->regularUser->id,
        'position' => 'manager',
        'department' => 'Operations',
    ]);
});

test('technician sees only tickets from assigned projects', function () {
    // Ticket in project A (assigned) — should appear
    Ticket::create([
        'user_id' => $this->regularUser->id,
        'project_id' => $this->projectA->id,
        'title' => 'Ticket in A',
        'priority' => 'low',
    ]);

    // Ticket in project B (not assigned) — should NOT appear
    Ticket::create([
        'user_id' => $this->regularUser->id,
        'project_id' => $this->projectB->id,
        'title' => 'Ticket in B',
        'priority' => 'low',
    ]);

    $response = $this->actingAs($this->technician)->get('/');
    $response->assertOk();

    $tickets = $response->original->getData()['page']['props']['tickets'];
    expect($tickets)->toHaveCount(1);
    expect($tickets[0]['title'])->toBe('Ticket in A');
});

test('non-technician sees only their own tickets', function () {
    // Own ticket
    Ticket::create([
        'user_id' => $this->regularUser->id,
        'project_id' => $this->projectA->id,
        'title' => 'My Ticket',
        'priority' => 'low',
    ]);

    // Other user's ticket
    Ticket::create([
        'user_id' => $this->technician->id,
        'project_id' => $this->projectA->id,
        'title' => 'Not Mine',
        'priority' => 'low',
    ]);

    $response = $this->actingAs($this->regularUser)->get('/');
    $response->assertOk();

    $tickets = $response->original->getData()['page']['props']['tickets'];
    expect($tickets)->toHaveCount(1);
    expect($tickets[0]['title'])->toBe('My Ticket');
});

test('technician can view ticket detail from assigned project', function () {
    $ticket = Ticket::create([
        'user_id' => $this->regularUser->id,
        'project_id' => $this->projectA->id,
        'title' => 'Viewable',
        'priority' => 'low',
    ]);

    $this->actingAs($this->technician)
        ->get("/tickets/{$ticket->id}/detail")
        ->assertOk();
});

test('home page passes isTechnician true for technicians', function () {
    $response = $this->actingAs($this->technician)->get('/');

    $isTechnician = $response->original->getData()['page']['props']['isTechnician'];
    expect($isTechnician)->toBeTrue();
});

test('home page passes isTechnician false for non-technicians', function () {
    $response = $this->actingAs($this->regularUser)->get('/');

    $isTechnician = $response->original->getData()['page']['props']['isTechnician'];
    expect($isTechnician)->toBeFalse();
});


