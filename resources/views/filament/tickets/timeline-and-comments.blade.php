@php
    $ticket = $getRecord();
    $authUser = auth()->user();
    $isItStaff = $authUser && $authUser->isItStaff();
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1rem;">
    <!-- Chronological Activity Timeline -->
    <div style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin: 0 0 1rem 0; font-size: 0.9375rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; max-width: 18px; max-height: 18px; display: inline-block;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Complaint Lifecycle &amp; Audit Timeline</span>
        </h3>

        <div style="position: relative; padding-left: 1.5rem; border-left: 2px solid #a7f3d0; display: flex; flex-direction: column; gap: 1.25rem;">
            @foreach($ticket->statusHistories as $history)
                @if($history->is_visible_to_complainant || $isItStaff)
                    <div style="position: relative;">
                        <!-- Dot on line -->
                        <div style="position: absolute; left: -1.9375rem; top: 0.25rem; width: 10px; height: 10px; border-radius: 50%; border: 2px solid #ffffff; background: #059669; box-shadow: 0 0 0 1px #059669;"></div>

                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px;">
                            <span style="font-size: 0.875rem; font-weight: 700; color: #0f172a;">
                                {{ $history->action_label }}
                            </span>
                            <span style="font-size: 0.75rem; color: #64748b;">
                                {{ $history->created_at->format('d M Y, h:i A') }} ({{ $history->created_at->diffForHumans() }})
                            </span>
                        </div>

                        <div style="margin-top: 4px; font-size: 0.8125rem; color: #475569;">
                            <span style="font-weight: 600; color: #334155;">
                                {{ $history->changedBy?->name ?? 'System' }}
                                @if($history->changedBy)
                                    <span style="color: #94a3b8; font-weight: normal;">({{ $history->changedBy->designation }})</span>
                                @endif
                            </span>
                            @if($history->comment)
                                <div style="margin-top: 6px; font-size: 0.8125rem; background: #f8fafc; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; color: #1e293b;">
                                    {{ $history->comment }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Communication & Comments Thread -->
    <div style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <h3 style="margin: 0 0 1rem 0; font-size: 0.9375rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; max-width: 18px; max-height: 18px; display: inline-block;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            <span>Communication &amp; Technical Notes</span>
        </h3>

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @php
                $commentsQuery = $ticket->comments();
                if (!$isItStaff) {
                    $commentsQuery->where('is_internal', false);
                }
                $comments = $commentsQuery->get();
            @endphp

            @forelse($comments as $comment)
                <div style="border-radius: 8px; padding: 12px 14px; border: 1px solid {{ $comment->is_internal ? '#fde68a' : '#e2e8f0' }}; background: {{ $comment->is_internal ? '#fffbeb' : '#f8fafc' }};">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-weight: 700; font-size: 0.8125rem; color: #0f172a;">
                                {{ $comment->user?->name ?? 'User' }}
                            </span>
                            <span style="font-size: 0.75rem; color: #64748b;">
                                ({{ $comment->user?->designation }})
                            </span>
                            @if($comment->is_internal)
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #fef3c7; color: #92400e;">
                                    Confidential Internal Note
                                </span>
                            @else
                                <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; background: #e0f2fe; color: #0369a1;">
                                    Public Update
                                </span>
                            @endif
                        </div>
                        <span style="font-size: 0.75rem; color: #94a3b8;">
                            {{ $comment->created_at->format('d M Y, h:i A') }}
                        </span>
                    </div>

                    <div style="font-size: 0.8125rem; color: #334155; white-space: pre-wrap; line-height: 1.5;">
                        {{ $comment->comment }}
                    </div>
                </div>
            @empty
                <p style="margin: 0; font-size: 0.8125rem; color: #94a3b8; font-style: italic;">No comments or technical notes recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
