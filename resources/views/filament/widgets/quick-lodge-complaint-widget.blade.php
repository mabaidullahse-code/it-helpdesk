@php
    $user = auth()->user();
@endphp

<div class="uhs-hero-root" style="grid-column: 1 / -1 !important; width: 100% !important; margin-bottom: 0.5rem;">
<style>
    .uhs-hero-dual-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 1.25rem !important;
        margin-bottom: 1.25rem !important;
        user-select: none;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    @media (max-width: 860px) {
        .uhs-hero-dual-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
    }

    .uhs-hero-card {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        padding: 1.5rem;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 220px;
        height: 100%;
        box-sizing: border-box;
    }

    .uhs-hero-welcome {
        background: linear-gradient(135deg, #064e3b 0%, #042f2e 50%, #0f172a 100%);
        box-shadow: 0 10px 25px -5px rgba(6, 78, 59, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
    }

    .uhs-hero-tracker {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #064e3b 100%);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
    }
</style>

<div class="uhs-hero-dual-grid">
    <!-- LEFT BOX: Welcome & Lodge New Complaint (Parallel Box 1) -->
    <div id="admin-welcome-card" class="uhs-hero-card uhs-hero-welcome">
        <!-- Interactive Canvas -->
        <canvas id="admin-stars-canvas" style="position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; opacity: 0.85;"></canvas>
        <div id="admin-nebula-glow" style="pointer-events: none; position: absolute; width: 300px; height: 300px; border-radius: 50%; background: radial-gradient(circle, rgba(52, 211, 153, 0.25) 0%, rgba(56, 189, 248, 0.15) 45%, transparent 70%); filter: blur(45px); transform: translate(-50%, -50%); opacity: 0; z-index: 1; transition: opacity 0.4s ease;"></div>

        <div style="position: relative; z-index: 10; display: flex; flex-direction: column; gap: 1rem; height: 100%; justify-content: space-between;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(52, 211, 153, 0.3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #34d399;"></span>
                    <span>IT Hardware, Networking & Systems Support</span>
                </div>
                <h2 style="font-size: 1.35rem; font-weight: 800; letter-spacing: -0.025em; color: #ffffff; margin: 0 0 0.35rem 0; line-height: 1.2;">
                    Welcome back, {{ $user?->name }}
                </h2>
                <p style="margin: 0; font-size: 0.8125rem; color: #cbd5e1; line-height: 1.5;">
                    Lodge complaints for internet connectivity, network cabling, computer hardware, printers, software, and IT peripherals. Track your complaint in real time from submission to confirmed resolution.
                </p>
            </div>

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; margin-top: 0.5rem;">
                <a href="{{ url('/admin/tickets/create') }}"
                   style="display: inline-flex; align-items: center; gap: 7px; border-radius: 0.5rem; background: #059669; padding: 9px 16px; font-size: 0.8125rem; font-weight: 700; color: #ffffff; text-decoration: none; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35); transition: all 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; display: inline-block;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Lodge New Complaint</span>
                </a>

                <a href="{{ url('/admin/knowledge-articles') }}"
                   style="display: inline-flex; align-items: center; gap: 6px; border-radius: 0.5rem; background: rgba(255, 255, 255, 0.12); padding: 9px 15px; font-size: 0.8125rem; font-weight: 600; color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.2); text-decoration: none; transition: all 0.2s ease;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; display: inline-block;">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <span>Troubleshooting Guides</span>
                </a>
            </div>
        </div>
    </div>

    <!-- RIGHT BOX: Check Complaint Status / Live Tracker (5 cols) -->
    <div id="admin-tracker-card" class="uhs-hero-card uhs-hero-tracker">
        
        <div style="position: relative; z-index: 10; display: flex; flex-direction: column; gap: 0.85rem; height: 100%; justify-content: space-between;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 700; background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #38bdf8;"></span>
                    <span>Live Complaint Tracker</span>
                </div>

                <h3 style="font-size: 1.25rem; font-weight: 800; letter-spacing: -0.02em; color: #ffffff; margin: 0 0 0.3rem 0; line-height: 1.2;">
                    Check Complaint Status
                </h3>
                <p style="margin: 0; font-size: 0.8125rem; color: #94a3b8; line-height: 1.45;">
                    Enter tracking ID to inspect real-time progress, assigned technician, and SLA resolution milestones.
                </p>
            </div>

            {{-- Interactive Search / Track Form --}}
            <form onsubmit="quickTrackTicket(event)" style="display: flex; gap: 8px; width: 100%; margin-top: 0.25rem; align-items: center; box-sizing: border-box;">
                <div style="position: relative; flex: 1; display: flex; align-items: center;">
                    <div style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; display: flex; align-items: center; justify-content: center; width: 16px; height: 16px; color: #94a3b8; z-index: 2;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <input type="text" id="quick-ticket-search-input" placeholder="e.g. ITHN-XXXXXX"
                           style="width: 100%; height: 38px; box-sizing: border-box; padding: 0 12px 0 36px; font-size: 0.8125rem; border-radius: 0.5rem; background: rgba(15, 23, 42, 0.75); border: 1px solid rgba(255, 255, 255, 0.22); color: #ffffff; outline: none; transition: border-color 0.15s ease, box-shadow 0.15s ease;"
                           onfocus="this.style.borderColor='#38bdf8'; this.style.boxShadow='0 0 0 2px rgba(56, 189, 248, 0.25)';"
                           onblur="this.style.borderColor='rgba(255, 255, 255, 0.22)'; this.style.boxShadow='none';" />
                </div>
                <button type="submit"
                        style="height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; background: #0284c7; color: #ffffff; border: none; border-radius: 0.5rem; padding: 0 16px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; transition: background-color 0.15s ease, transform 0.15s ease; white-space: nowrap; flex-shrink: 0;"
                        onmouseover="this.style.background='#0369a1'; this.style.transform='translateY(-1px)';"
                        onmouseout="this.style.background='#0284c7'; this.style.transform='none';">
                    <span>Track</span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>

            {{-- Quick Status Filter Shortcuts --}}
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 6px; font-size: 0.6875rem;">
                <span style="color: #64748b; font-weight: 600;">Quick Filters:</span>
                <a href="{{ url('/admin/tickets?tableFilters[status][values][0]=in_progress') }}"
                   style="padding: 2px 7px; border-radius: 4px; background: rgba(14, 165, 233, 0.15); color: #7dd3fc; border: 1px solid rgba(56, 189, 248, 0.25); text-decoration: none;">
                    In Progress
                </a>
                <a href="{{ url('/admin/tickets?tableFilters[status][values][0]=awaiting_confirmation') }}"
                   style="padding: 2px 7px; border-radius: 4px; background: rgba(168, 85, 247, 0.15); color: #d8b4fe; border: 1px solid rgba(192, 132, 252, 0.25); text-decoration: none;">
                    Awaiting User
                </a>
                <a href="{{ url('/admin/tickets') }}"
                   style="padding: 2px 7px; border-radius: 4px; background: rgba(255, 255, 255, 0.08); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.15); text-decoration: none;">
                    All Tickets &rarr;
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function quickTrackTicket(e) {
    e.preventDefault();
    const input = document.getElementById('quick-ticket-search-input');
    if (!input || !input.value.trim()) return;
    const trackingId = input.value.trim();
    window.location.href = "{{ url('/admin/tickets') }}?tableSearch=" + encodeURIComponent(trackingId);
}

(function() {
    function initDashboardStars() {
        const card = document.getElementById('admin-welcome-card');
        const canvas = document.getElementById('admin-stars-canvas');
        const glow = document.getElementById('admin-nebula-glow');
        if (!card || !canvas) return;

        const ctx = canvas.getContext('2d');
        let width = 0, height = 0;
        let stars = [];
        let mouse = { x: -1000, y: -1000, targetX: -1000, targetY: -1000, isHovered: false };

        const colors = [
            'rgba(255, 255, 255, ',
            'rgba(52, 211, 153, ',
            'rgba(56, 189, 248, '
        ];

        function resize() {
            const rect = card.getBoundingClientRect();
            width = canvas.width = rect.width;
            height = canvas.height = rect.height;
            stars = [];
            const count = Math.min(45, Math.floor(width / 22));
            for (let i = 0; i < count; i++) {
                stars.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.35,
                    vy: (Math.random() - 0.5) * 0.35,
                    radius: Math.random() * 1.5 + 0.5,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    alpha: Math.random() * 0.6 + 0.3,
                    speed: (Math.random() * 0.01 + 0.005) * (Math.random() < 0.5 ? 1 : -1)
                });
            }
        }

        function animate() {
            ctx.clearRect(0, 0, width, height);

            mouse.x += (mouse.targetX - mouse.x) * 0.12;
            mouse.y += (mouse.targetY - mouse.y) * 0.12;

            if (glow && mouse.isHovered) {
                glow.style.left = mouse.x + 'px';
                glow.style.top = mouse.y + 'px';
                glow.style.opacity = '1';
            }

            for (let i = 0; i < stars.length; i++) {
                for (let j = i + 1; j < stars.length; j++) {
                    const dx = stars[i].x - stars[j].x;
                    const dy = stars[i].y - stars[j].y;
                    const d = Math.sqrt(dx * dx + dy * dy);
                    if (d < 70) {
                        ctx.beginPath();
                        ctx.moveTo(stars[i].x, stars[i].y);
                        ctx.lineTo(stars[j].x, stars[j].y);
                        ctx.strokeStyle = 'rgba(52, 211, 153, ' + ((1 - d / 70) * 0.18) + ')';
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }

                if (mouse.isHovered) {
                    const dx = stars[i].x - mouse.x;
                    const dy = stars[i].y - mouse.y;
                    const d = Math.sqrt(dx * dx + dy * dy);
                    if (d < 100) {
                        ctx.beginPath();
                        ctx.moveTo(stars[i].x, stars[i].y);
                        ctx.lineTo(mouse.x, mouse.y);
                        ctx.strokeStyle = 'rgba(56, 189, 248, ' + ((1 - d / 100) * 0.35) + ')';
                        ctx.lineWidth = 0.7;
                        ctx.stroke();

                        stars[i].x += (dx / d) * (1 - d / 100) * 0.6;
                        stars[i].y += (dy / d) * (1 - d / 100) * 0.6;
                    }
                }

                stars[i].x += stars[i].vx;
                stars[i].y += stars[i].vy;
                if (stars[i].x < 0) stars[i].x = width;
                if (stars[i].x > width) stars[i].x = 0;
                if (stars[i].y < 0) stars[i].y = height;
                if (stars[i].y > height) stars[i].y = 0;

                stars[i].alpha += stars[i].speed;
                if (stars[i].alpha >= 0.95 || stars[i].alpha <= 0.2) stars[i].speed *= -1;

                ctx.beginPath();
                ctx.arc(stars[i].x, stars[i].y, stars[i].radius, 0, Math.PI * 2);
                ctx.fillStyle = stars[i].color + stars[i].alpha + ')';
                ctx.shadowBlur = 4;
                ctx.shadowColor = stars[i].color + '0.8)';
                ctx.fill();
                ctx.shadowBlur = 0;
            }

            requestAnimationFrame(animate);
        }

        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            mouse.targetX = e.clientX - rect.left;
            mouse.targetY = e.clientY - rect.top;
            mouse.isHovered = true;
        });

        card.addEventListener('mouseenter', function() {
            mouse.isHovered = true;
        });

        card.addEventListener('mouseleave', function() {
            mouse.isHovered = false;
            if (glow) glow.style.opacity = '0';
        });

        window.addEventListener('resize', resize);
        resize();
        requestAnimationFrame(animate);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardStars);
    } else {
        initDashboardStars();
    }
})();
</script>
</div>
