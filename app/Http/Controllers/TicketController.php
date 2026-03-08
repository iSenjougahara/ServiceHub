<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTicketDetails;
use App\Jobs\SendTicketNotification;
use App\Mail\NewTicketNotification;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'project_id' => ['required', 'exists:projects,id'],
            'details_file' => ['nullable', 'file', 'mimes:json', 'max:2048'],
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
        ]);

        if ($request->hasFile('details_file')) {
            $path = $request->file('details_file')->store('ticket-details');
            ProcessTicketDetails::dispatch($ticket, $path);
        } else {
            $ticket->load('project', 'user', 'detail');
            $projectEmail = $ticket->project->email;
            if ($projectEmail) {
                Mail::to($projectEmail)->queue(new NewTicketNotification($ticket));
            }
        }

        return redirect('/')->with('success', 'Ticket created successfully.');
    }
}
