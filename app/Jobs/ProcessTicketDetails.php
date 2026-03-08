<?php

namespace App\Jobs;

use App\Mail\NewTicketNotification;
use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessTicketDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $filePath,
    ) {}

    public function handle(): void
    {
        $json = Storage::get($this->filePath);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            Log::error("Invalid JSON in ticket details file for ticket #{$this->ticket->id}");
            Storage::delete($this->filePath);
            return;
        }

        $allowed = [
            'technical_notes',
            'environment',
            'browser',
            'operating_system',
            'steps_to_reproduce',
            'expected_behavior',
            'actual_behavior',
            'resolution',
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        TicketDetail::updateOrCreate(
            ['ticket_id' => $this->ticket->id],
            $filtered,
        );

        Storage::delete($this->filePath);

        $this->ticket->load('project', 'user', 'detail');
        $projectEmail = $this->ticket->project->email;
        if ($projectEmail) {
            Mail::to($projectEmail)->send(new NewTicketNotification($this->ticket));
        }
    }
}
