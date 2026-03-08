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
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = auth()->user();
        $profile = $user->profile;
        $isTechnician = $profile && $profile->position === 'technician';

        if ($isTechnician) {
            $tickets = Ticket::with('project', 'user')->latest()->get();
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
