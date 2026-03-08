<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserProfile;
use App\Jobs\ProcessTicketDetails;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

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
        'description' => 'A test project',
        'email' => 'project@corp.com',
        'status' => 'active',
        'start_date' => '2025-01-01',
    ]);

    $this->user = User::factory()->create();
    UserProfile::create([
        'user_id' => $this->user->id,
        'position' => 'manager',
        'department' => 'Operations',
    ]);
});

test('authenticated user can create a ticket', function () {
    Queue::fake();

    $this->actingAs($this->user)
        ->post('/tickets', [
            'title' => 'Test Ticket',
            'description' => 'Something is broken',
            'priority' => 'high',
            'project_id' => $this->project->id,
        ])
        ->assertRedirect('/');

    expect(Ticket::count())->toBe(1);

    $ticket = Ticket::first();
    expect($ticket->title)->toBe('Test Ticket');
    expect($ticket->priority)->toBe('high');
    expect($ticket->status)->toBe('open');
    expect($ticket->user_id)->toBe($this->user->id);
    expect($ticket->project_id)->toBe($this->project->id);
});

test('ticket creation validates required fields', function () {
    $this->actingAs($this->user)
        ->post('/tickets', [])
        ->assertSessionHasErrors(['title', 'priority', 'project_id']);
});

test('ticket creation validates priority values', function () {
    $this->actingAs($this->user)
        ->post('/tickets', [
            'title' => 'Test',
            'priority' => 'ultra',
            'project_id' => $this->project->id,
        ])
        ->assertSessionHasErrors('priority');
});

test('ticket creation validates project exists', function () {
    $this->actingAs($this->user)
        ->post('/tickets', [
            'title' => 'Test',
            'priority' => 'low',
            'project_id' => 9999,
        ])
        ->assertSessionHasErrors('project_id');
});

test('ticket creation with json file dispatches job', function () {
    Queue::fake();
    Storage::fake('local');

    $file = UploadedFile::fake()->createWithContent(
        'details.json',
        json_encode(['environment' => 'production', 'browser' => 'Chrome'])
    );

    $this->actingAs($this->user)
        ->post('/tickets', [
            'title' => 'Ticket with details',
            'priority' => 'medium',
            'project_id' => $this->project->id,
            'details_file' => $file,
        ])
        ->assertRedirect('/');

    Queue::assertPushed(ProcessTicketDetails::class);
});

test('guest cannot create a ticket', function () {
    $this->post('/tickets', [
        'title' => 'Test',
        'priority' => 'low',
        'project_id' => $this->project->id,
    ])->assertRedirect('/login');

    expect(Ticket::count())->toBe(0);
});

test('user can view their own ticket detail', function () {
    $ticket = Ticket::create([
        'user_id' => $this->user->id,
        'project_id' => $this->project->id,
        'title' => 'My Ticket',
        'priority' => 'low',
    ]);

    $this->actingAs($this->user)
        ->get("/tickets/{$ticket->id}/detail")
        ->assertOk();
});

test('user cannot view another users ticket detail', function () {
    $otherUser = User::factory()->create();
    UserProfile::create([
        'user_id' => $otherUser->id,
        'position' => 'manager',
    ]);

    $ticket = Ticket::create([
        'user_id' => $otherUser->id,
        'project_id' => $this->project->id,
        'title' => 'Other Ticket',
        'priority' => 'low',
    ]);

    $this->actingAs($this->user)
        ->get("/tickets/{$ticket->id}/detail")
        ->assertForbidden();
});
