<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Filter Form -->
        <div style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <form wire:submit="applyFilters" style="display: flex; flex-direction: column; gap: 1rem;">
                {{ $this->form }}
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                    <x-filament::button type="submit" color="primary">
                        Apply Report Filters
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- KPI Statistic Cards -->
        @php $stats = $this->stats; @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
            <div style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Total Complaints</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $stats['total'] }}</div>
            </div>

            <div style="border-radius: 12px; border: 1px solid #bae6fd; background: #f0f9ff; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #0284c7; text-transform: uppercase; letter-spacing: 0.05em;">Active / Open</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #0369a1;">{{ $stats['open'] }}</div>
            </div>

            <div style="border-radius: 12px; border: 1px solid #fecaca; background: #fef2f2; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em;">Overdue / SLA Alert</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #b91c1c;">{{ $stats['overdue'] }}</div>
            </div>

            <div style="border-radius: 12px; border: 1px solid #e9d5ff; background: #faf5ff; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #9333ea; text-transform: uppercase; letter-spacing: 0.05em;">Awaiting Confirm</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #7e22ce;">{{ $stats['confirmation'] }}</div>
            </div>

            <div style="border-radius: 12px; border: 1px solid #fde68a; background: #fffbeb; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.05em;">Reopened</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #b45309;">{{ $stats['reopened'] }}</div>
            </div>

            <div style="border-radius: 12px; border: 1px solid #a7f3d0; background: #ecfdf5; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="font-size: 11px; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.05em;">Confirmed Closed</div>
                <div style="margin-top: 6px; font-size: 1.5rem; font-weight: 800; color: #047857;">{{ $stats['closed'] }} ({{ $stats['rate'] }}%)</div>
            </div>
        </div>

        <!-- Filtered Records Data Table -->
        <div style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
            <div style="padding: 12px 16px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin: 0; font-size: 0.9375rem; font-weight: 700; color: #0f172a;">
                    Filtered Complaint Records (Preview)
                </h3>
                <span style="font-size: 12px; color: #64748b;">Showing up to 15 recent filtered entries</span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 10px 14px;">Tracking ID</th>
                            <th style="padding: 10px 14px;">Title & Complainant</th>
                            <th style="padding: 10px 14px;">Department</th>
                            <th style="padding: 10px 14px;">Category</th>
                            <th style="padding: 10px 14px;">Priority</th>
                            <th style="padding: 10px 14px;">Status</th>
                            <th style="padding: 10px 14px;">Assigned (PKT)</th>
                            <th style="padding: 10px 14px;">Processed (PKT)</th>
                            <th style="padding: 10px 14px;">Completed (PKT)</th>
                            <th style="padding: 10px 14px;">Final Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->recentTickets as $ticket)
                            @php
                                $completedDate = $ticket->completed_at ?? $ticket->confirmed_resolved_at;
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9; color: #334155;">
                                <td style="padding: 10px 14px; font-weight: 700; color: #059669; vertical-align: top;">
                                    <a href="{{ url('/admin/tickets/' . $ticket->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ $ticket->tracking_id }}
                                    </a>
                                    @if($ticket->submitted_at)
                                        <div style="font-size: 11px; color: #64748b; font-weight: 400; margin-top: 3px;">
                                            Sub: {{ $ticket->submitted_at->timezone('Asia/Karachi')->format('d/m/Y h:i A') }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; font-weight: 600; color: #0f172a; vertical-align: top;">
                                    {{ Str::limit($ticket->title, 35) }}
                                    <div style="font-size: 11px; color: #64748b; font-weight: 400; margin-top: 2px;">
                                        By: {{ $ticket->complainant?->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top;">{{ $ticket->department?->name }}</td>
                                <td style="padding: 10px 14px; vertical-align: top;">{{ $ticket->category?->name }}</td>
                                <td style="padding: 10px 14px; vertical-align: top;">
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700;
                                        @if($ticket->priority === 'critical') background: #fee2e2; color: #991b1b;
                                        @elseif($ticket->priority === 'high') background: #fef3c7; color: #92400e;
                                        @elseif($ticket->priority === 'medium') background: #e0f2fe; color: #0369a1;
                                        @else background: #f1f5f9; color: #475569; @endif">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top;">
                                    <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700;
                                        @if($ticket->status === 'closed') background: #dcfce7; color: #166534;
                                        @elseif($ticket->status === 'reopened') background: #fee2e2; color: #991b1b;
                                        @elseif($ticket->status === 'in_progress') background: #e0f2fe; color: #0369a1;
                                        @elseif($ticket->status === 'awaiting_confirmation') background: #f3e8ff; color: #6b21a8;
                                        @else background: #f1f5f9; color: #475569; @endif">
                                        {{ ucwords(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top;">
                                    <div style="font-weight: 600; color: #0f172a;">{{ $ticket->assignedTechnician?->name ?? 'Unassigned' }}</div>
                                    @if($ticket->assigned_at)
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                            {{ $ticket->assigned_at->timezone('Asia/Karachi')->format('d/m/Y h:i A') }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top;">
                                    @if($ticket->work_started_at)
                                        <div style="font-size: 12px; font-weight: 500; color: #0f172a;">
                                            {{ $ticket->work_started_at->timezone('Asia/Karachi')->format('d/m/Y h:i A') }}
                                        </div>
                                    @else
                                        <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Pending Start</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top;">
                                    @if($completedDate)
                                        <div style="font-size: 12px; font-weight: 500; color: #0f172a;">
                                            {{ $completedDate->timezone('Asia/Karachi')->format('d/m/Y h:i A') }}
                                        </div>
                                    @else
                                        <span style="font-size: 11px; color: #94a3b8; font-style: italic;">In Progress</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 14px; vertical-align: top; max-width: 220px;">
                                    @if($ticket->resolution_summary)
                                        <div style="font-size: 12px; color: #0f172a; line-height: 1.35; background: #f8fafc; padding: 4px 8px; border-left: 3px solid #1e3a8a; border-radius: 4px;">
                                            {{ Str::limit($ticket->resolution_summary, 90) }}
                                        </div>
                                    @elseif($ticket->status === 'closed')
                                        <span style="color: #166534; font-weight: 600; font-size: 12px;">Confirmed Resolved</span>
                                    @elseif($ticket->status === 'awaiting_confirmation')
                                        <span style="color: #0284c7; font-weight: 600; font-size: 12px;">Work Done (Pending Confirm)</span>
                                    @else
                                        <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Troubleshooting in progress</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 24px; text-align: center; color: #94a3b8; font-style: italic;">
                                    No records match the current filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
