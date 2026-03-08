<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TicketController;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', function () {
        return Inertia::render('Auth/Register', [
            'projects' => Project::all(['id', 'name']),
        ]);
    })->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = auth()->user();
        $profile = $user->profile;
        $isTechnician = $profile && $profile->position === 'technician';

        if ($isTechnician) {
            $projectIds = $profile->projects()->pluck('projects.id');
            $tickets = Ticket::with('project', 'user')
                ->whereIn('project_id', $projectIds)
                ->latest()
                ->get();
        } else {
            $tickets = $user->tickets()->with('project')->latest()->get();
        }

        return Inertia::render('Home', [
            'profile' => $profile,
            'tickets' => $tickets,
            'isTechnician' => $isTechnician,
            'projects' => Project::all(['id', 'name']),
        ]);
    });

    Route::get('/tickets/{ticket}/detail', function (Ticket $ticket) {
        $user = auth()->user();
        $isTechnician = $user->profile && $user->profile->position === 'technician';
        abort_unless($isTechnician || $ticket->user_id === $user->id, 403);
        return response()->json($ticket->detail);
    });

    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

    Route::post('/tickets/{ticket}/take', function (Ticket $ticket) {
        $user = auth()->user();
        $profile = $user->profile;
        abort_unless($profile && $profile->position === 'technician', 403);
        abort_unless(in_array($ticket->status, ['open', 'in_progress']), 422);
        $ticket->update(['status' => 'in_progress']);
        return redirect('/');
    })->name('tickets.take');

    Route::post('/tickets/{ticket}/resolve', function (Ticket $ticket) {
        $user = auth()->user();
        $profile = $user->profile;
        abort_unless($profile && $profile->position === 'technician', 403);
        abort_unless(in_array($ticket->status, ['open', 'in_progress']), 422);
        $ticket->update(['status' => 'resolved']);
        return redirect('/');
    })->name('tickets.resolve');

    Route::post('/tickets/{ticket}/close', function (Ticket $ticket) {
        $user = auth()->user();
        $profile = $user->profile;
        abort_unless($profile && $profile->position === 'technician', 403);
        abort_unless(in_array($ticket->status, ['open', 'in_progress']), 422);
        $ticket->update(['status' => 'closed']);
        return redirect('/');
    })->name('tickets.close');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
