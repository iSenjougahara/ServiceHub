<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #1a56db; font-size: 22px; }
        h2 { color: #555; font-size: 16px; margin-top: 24px; border-bottom: 1px solid #ddd; padding-bottom: 6px; }
        .field { margin-bottom: 8px; }
        .label { font-weight: bold; color: #555; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .status { background: #dbeafe; color: #1e40af; }
        .priority-low { color: #16a34a; }
        .priority-medium { color: #ca8a04; }
        .priority-high { color: #ea580c; }
        .priority-critical { color: #dc2626; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>New Ticket: {{ $ticket->title }}</h1>

        <h2>Ticket Information</h2>
        <div class="field">
            <span class="label">ID:</span> #{{ $ticket->id }}
        </div>
        <div class="field">
            <span class="label">Status:</span>
            <span class="badge status">{{ str_replace('_', ' ', $ticket->status) }}</span>
        </div>
        <div class="field">
            <span class="label">Priority:</span>
            <span class="priority-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
        </div>
        <div class="field">
            <span class="label">Created by:</span> {{ $ticket->user->name }} ({{ $ticket->user->email }})
        </div>
        <div class="field">
            <span class="label">Project:</span> {{ $ticket->project->name }}
        </div>
        <div class="field">
            <span class="label">Description:</span><br>
            {{ $ticket->description ?? 'No description provided.' }}
        </div>

        @if($ticket->detail)
        <h2>Technical Details</h2>
        @if($ticket->detail->environment)
        <div class="field">
            <span class="label">Environment:</span> {{ $ticket->detail->environment }}
        </div>
        @endif
        @if($ticket->detail->browser)
        <div class="field">
            <span class="label">Browser:</span> {{ $ticket->detail->browser }}
        </div>
        @endif
        @if($ticket->detail->operating_system)
        <div class="field">
            <span class="label">Operating System:</span> {{ $ticket->detail->operating_system }}
        </div>
        @endif
        @if($ticket->detail->technical_notes)
        <div class="field">
            <span class="label">Technical Notes:</span><br>
            {{ $ticket->detail->technical_notes }}
        </div>
        @endif
        @if($ticket->detail->steps_to_reproduce)
        <div class="field">
            <span class="label">Steps to Reproduce:</span><br>
            {!! nl2br(e($ticket->detail->steps_to_reproduce)) !!}
        </div>
        @endif
        @if($ticket->detail->expected_behavior)
        <div class="field">
            <span class="label">Expected Behavior:</span><br>
            {{ $ticket->detail->expected_behavior }}
        </div>
        @endif
        @if($ticket->detail->actual_behavior)
        <div class="field">
            <span class="label">Actual Behavior:</span><br>
            {{ $ticket->detail->actual_behavior }}
        </div>
        @endif
        @if($ticket->detail->resolution)
        <div class="field">
            <span class="label">Resolution:</span><br>
            {{ $ticket->detail->resolution }}
        </div>
        @endif
        @endif

        <div class="footer">
            This is an automated notification from ServiceHub.
        </div>
    </div>
</body>
</html>
