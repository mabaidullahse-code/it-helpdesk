<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Ticket Report - {{ $ticket->tracking_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 20px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            color: #1e3a8a;
            margin: 0;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 11px;
            color: #64748b;
        }
        .tracking-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .tracking-box table {
            width: 100%;
        }
        .tracking-id {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-table td.label {
            width: 25%;
            font-weight: bold;
            color: #475569;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .info-table td.value {
            width: 75%;
            border: 1px solid #e2e8f0;
        }
        .timeline-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .timeline-table th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }
        .timeline-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>IT Helpdesk & Service Desk Portal</h1>
        <div class="subtitle">Official Complaint & Resolution Summary Report</div>
    </div>

    <div class="tracking-box">
        <table>
            <tr>
                <td>
                    <span class="tracking-id">{{ $ticket->tracking_id }}</span><br>
                    <strong style="font-size: 13px;">{{ $ticket->title }}</strong>
                </td>
                <td style="text-align: right;">
                    <strong>Status:</strong> {{ ucwords(str_replace('_', ' ', $ticket->status)) }}<br>
                    <strong>Priority:</strong> {{ ucfirst($ticket->priority) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Complainant & Location Details</div>
    <table class="info-table">
        <tr>
            <td class="label">Complainant Name:</td>
            <td class="value">{{ $ticket->complainant?->name }} ({{ $ticket->complainant?->designation }})</td>
        </tr>
        <tr>
            <td class="label">Department:</td>
            <td class="value">{{ $ticket->department?->name }}</td>
        </tr>
        <tr>
            <td class="label">Contact / Phone:</td>
            <td class="value">{{ $ticket->contact_number ?: ($ticket->complainant?->phone ?: 'N/A') }}</td>
        </tr>
        <tr>
            <td class="label">Location / Room:</td>
            <td class="value">{{ $ticket->location ?: 'N/A' }} {{ $ticket->room_number ? '(Room/Office: ' . $ticket->room_number . ')' : '' }}</td>
        </tr>
    </table>

    <div class="section-title">Technical Incident Details</div>
    <table class="info-table">
        <tr>
            <td class="label">Category:</td>
            <td class="value">{{ $ticket->category?->name }} {{ $ticket->subcategory ? '— ' . $ticket->subcategory->name : '' }}</td>
        </tr>
        <tr>
            <td class="label">Asset / Serial:</td>
            <td class="value">{{ $ticket->asset_tag_or_serial ?: ($ticket->asset?->asset_tag ?: 'N/A') }}</td>
        </tr>
        <tr>
            <td class="label">Issue Description:</td>
            <td class="value">{{ $ticket->description }}</td>
        </tr>
        @if($ticket->additional_info)
        <tr>
            <td class="label">Additional Notes:</td>
            <td class="value">{{ $ticket->additional_info }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Assignment & Technical Resolution</div>
    <table class="info-table">
        <tr>
            <td class="label">Assigned Technician:</td>
            <td class="value">{{ $ticket->assignedTechnician ? $ticket->assignedTechnician->name . ' (' . ($ticket->assignedTechnician->designation ?? 'IT Staff') . ')' : 'Unassigned' }}</td>
        </tr>
        <tr>
            <td class="label">Assigned Date & Time:</td>
            <td class="value">{{ $ticket->assigned_at ? $ticket->assigned_at->timezone('Asia/Karachi')->format('d M Y, h:i A') . ' PKT' : 'Pending Assignment' }}</td>
        </tr>
        <tr>
            <td class="label">Submission Date & Time:</td>
            <td class="value">{{ $ticket->submitted_at ? $ticket->submitted_at->timezone('Asia/Karachi')->format('d M Y, h:i A') . ' PKT' : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Work Started (Processed):</td>
            <td class="value">{{ $ticket->work_started_at ? $ticket->work_started_at->timezone('Asia/Karachi')->format('d M Y, h:i A') . ' PKT' : 'Not Started' }}</td>
        </tr>
        <tr>
            <td class="label">Work Completed Date:</td>
            <td class="value">{{ $ticket->completed_at ? $ticket->completed_at->timezone('Asia/Karachi')->format('d M Y, h:i A') . ' PKT' : 'Pending' }}</td>
        </tr>
        @if($ticket->confirmed_resolved_at)
        <tr>
            <td class="label">Confirmed Resolved Date:</td>
            <td class="value" style="color: #166534; font-weight: 600;">{{ $ticket->confirmed_resolved_at->timezone('Asia/Karachi')->format('d M Y, h:i A') }} PKT</td>
        </tr>
        @endif
        @if($ticket->resolution_summary)
        <tr>
            <td class="label">Final Result / Resolution:</td>
            <td class="value"><strong style="color: #0f172a;">{{ $ticket->resolution_summary }}</strong></td>
        </tr>
        @endif
        @if($ticket->reopen_reason)
        <tr>
            <td class="label">Reopen History Reason:</td>
            <td class="value" style="color: #b91c1c;">{{ $ticket->reopen_reason }}</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Timeline & Audit Activity</div>
    <table class="timeline-table">
        <thead>
            <tr>
                <th style="width: 25%;">Timestamp (PKT)</th>
                <th style="width: 25%;">Action</th>
                <th style="width: 20%;">Actor</th>
                <th style="width: 30%;">Details / Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ticket->statusHistories as $history)
            <tr>
                <td>{{ $history->created_at->timezone('Asia/Karachi')->format('d/m/Y h:i A') }} PKT</td>
                <td><strong>{{ $history->action_label }}</strong></td>
                <td>{{ $history->changedBy?->name ?? 'System' }}</td>
                <td>{{ $history->comment }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ $generatedAt }} — IT Hardware & Networking Helpdesk Management System
    </div>
</body>
</html>
