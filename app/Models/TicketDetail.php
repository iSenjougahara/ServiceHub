<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDetail extends Model
{
    protected $fillable = [
        'ticket_id',
        'technical_notes',
        'environment',
        'browser',
        'operating_system',
        'steps_to_reproduce',
        'expected_behavior',
        'actual_behavior',
        'resolution',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
