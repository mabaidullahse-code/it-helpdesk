<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UHS IT Helpdesk & Service Desk Portal — University of Health Sciences Lahore</title>
    <meta name="description" content="Official IT Helpdesk and E-Ticketing System of University of Health Sciences (UHS) Lahore for Hardware, Networking, Systems, and Technical Support.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for instant, pristine rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        uhs: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc7fb',
                            400: '#36abf7',
                            500: '#0c8fe9',
                            600: '#0171c7',
                            700: '#025aa1',
                            800: '#064d85',
                            900: '#0b416f',
                            950: '#072949',
                        },
                        navy: {
                            800: '#0f172a',
                            900: '#0b132b',
                            950: '#060b1a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-dark {
            background: rgba(11, 19, 43, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .hero-pattern {
            background-color: #0b132b;
            background-image: radial-gradient(rgba(14, 165, 233, 0.15) 1px, transparent 0);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-uhs-500 selection:text-white overflow-x-hidden">

    <!-- TOP INSTITUTIONAL BAR -->
    <div class="bg-navy-950 text-slate-300 text-xs py-2 px-4 border-b border-white/10">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2 text-[11px] sm:text-xs">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse flex-shrink-0"></span>
                <span class="font-medium text-slate-200">University of Health Sciences, Lahore</span>
                <span class="text-slate-500 hidden sm:inline">&bull;</span>
                <span class="text-slate-400 hidden sm:inline">Directorate of Information Technology</span>
            </div>
            <div class="flex items-center gap-3 sm:gap-4 text-[11px] sm:text-xs text-slate-400">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>Support Hotline: Ext <strong class="text-white font-semibold">304, 305</strong></span>
                </span>
                <span>&bull;</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <a href="mailto:it.support@uhs.edu.pk" class="text-slate-300 hover:text-white transition">it.support@uhs.edu.pk</a>
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN NAVIGATION -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition w-full">
        <div class="w-full max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-3">
                <!-- LOGO & BRAND -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group flex-shrink-0">
                    <img src="{{ asset('images/uhs-logo.png') }}" 
                         alt="University of Health Sciences Lahore" 
                         class="w-11 h-11 sm:w-12 sm:h-12 object-contain flex-shrink-0 drop-shadow-sm transition-transform duration-200 group-hover:scale-105"
                         style="max-width: 48px; max-height: 48px;">
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-base sm:text-lg text-slate-900 tracking-tight whitespace-nowrap font-display">
                                University of Health Sciences
                            </span>
                            <span class="hidden sm:inline-flex text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap">
                                IT Helpdesk
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-medium whitespace-nowrap flex items-center gap-1.5">
                            <span>Directorate of IT</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>Hardware &amp; Network Division</span>
                        </div>
                    </div>
                </a>

                <!-- NAV LINKS (Full on 2XL screens) -->
                <nav class="hidden 2xl:flex items-center gap-1 text-sm font-semibold text-slate-600">
                    <a href="#track" class="px-3 py-1.5 rounded-lg hover:text-uhs-700 hover:bg-slate-100 transition whitespace-nowrap">Track Complaint</a>
                    <a href="#services" class="px-3 py-1.5 rounded-lg hover:text-uhs-700 hover:bg-slate-100 transition whitespace-nowrap">IT Services</a>
                    <a href="#how-it-works" class="px-3 py-1.5 rounded-lg hover:text-uhs-700 hover:bg-slate-100 transition whitespace-nowrap">Workflow</a>
                    <a href="#knowledge" class="px-3 py-1.5 rounded-lg hover:text-uhs-700 hover:bg-slate-100 transition whitespace-nowrap">Self-Help Guides</a>
                    <a href="#helpline" class="px-3 py-1.5 rounded-lg hover:text-uhs-700 hover:bg-slate-100 transition whitespace-nowrap">Emergency Contacts</a>
                </nav>

                <!-- ACTIONS & MOBILE MENU TOGGLE -->
                <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <!-- Quick Track Link for medium screens -->
                    <a href="#track" 
                       class="hidden md:inline-flex 2xl:hidden items-center gap-1.5 px-3 py-2 text-xs font-semibold text-uhs-800 bg-uhs-50 hover:bg-uhs-100 rounded-xl border border-uhs-200/80 transition whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-uhs-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Track</span>
                    </a>

                    <a href="{{ url('/admin/login') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:text-uhs-800 bg-slate-100 hover:bg-slate-200/80 rounded-xl border border-slate-200/70 transition whitespace-nowrap shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="whitespace-nowrap">Staff Login</span>
                    </a>

                    <a href="{{ url('/admin/tickets/create') }}"
                       class="inline-flex items-center gap-1.5 sm:gap-2 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-uhs-700 via-uhs-800 to-navy-900 hover:from-uhs-800 hover:to-navy-950 text-white font-bold text-xs sm:text-sm shadow-md shadow-uhs-900/20 hover:shadow-lg hover:shadow-uhs-900/30 hover:-translate-y-0.5 active:translate-y-0 transition-all whitespace-nowrap">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="whitespace-nowrap">Lodge Complaint</span>
                    </a>

                    <!-- Hamburger Button for screens below 2XL -->
                    <button type="button" 
                            onclick="const m = document.getElementById('header-nav-menu'); m.classList.toggle('hidden');"
                            class="2xl:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200/70 transition"
                            aria-label="Navigation Menu">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- DROPDOWN NAV MENU (For screens below 2XL) -->
        <div id="header-nav-menu" class="hidden 2xl:hidden border-t border-slate-200/80 bg-white/98 backdrop-blur-md px-4 py-3 shadow-lg">
            <div class="max-w-7xl mx-auto flex flex-col gap-1 text-sm font-semibold text-slate-700">
                <a href="#track" onclick="document.getElementById('header-nav-menu').classList.add('hidden')" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-uhs-700 transition">Track Complaint</a>
                <a href="#services" onclick="document.getElementById('header-nav-menu').classList.add('hidden')" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-uhs-700 transition">IT Services</a>
                <a href="#how-it-works" onclick="document.getElementById('header-nav-menu').classList.add('hidden')" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-uhs-700 transition">Workflow</a>
                <a href="#knowledge" onclick="document.getElementById('header-nav-menu').classList.add('hidden')" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-uhs-700 transition">Self-Help Guides</a>
                <a href="#helpline" onclick="document.getElementById('header-nav-menu').classList.add('hidden')" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-uhs-700 transition">Emergency Contacts</a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION WITH INTERACTIVE COSMIC NEBULA & STAR UNIVERSE -->
    <section class="hero-pattern relative text-white pt-20 pb-28 overflow-hidden" id="hero-cosmic-universe">
        <!-- Interactive Star Universe Canvas -->
        <canvas id="cosmic-stars-canvas" class="absolute inset-0 w-full h-full pointer-events-none z-0" style="opacity: 0.95;"></canvas>

        <!-- Interactive Nebula Cloud Following Mouse -->
        <div id="cosmic-nebula-glow" 
             class="absolute w-[500px] h-[500px] rounded-full pointer-events-none blur-[100px] transition-opacity duration-300 ease-out z-0"
             style="background: radial-gradient(circle, rgba(16, 185, 129, 0.28) 0%, rgba(14, 165, 233, 0.20) 35%, rgba(99, 102, 241, 0.12) 65%, transparent 75%); transform: translate(-50%, -50%); opacity: 0; top: 50%; left: 50%;"></div>

        <!-- Ambient Ambient Lighting -->
        <div class="absolute -top-32 left-1/4 w-96 h-96 bg-uhs-500/20 rounded-full blur-3xl pointer-events-none z-0"></div>
        <div class="absolute top-1/2 right-10 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none z-0"></div>
        <div class="absolute -bottom-20 left-1/3 w-[500px] h-[250px] bg-emerald-500/15 rounded-full blur-3xl pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-uhs-300 border border-white/10 text-xs font-semibold backdrop-blur-sm mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>UHS Institutional IT Service Management System</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight font-display leading-tight sm:leading-tight mb-6">
                    Fast, Transparent & Reliable <br class="hidden sm:inline">
                    <span class="bg-gradient-to-r from-uhs-300 via-sky-200 to-indigo-300 bg-clip-text text-transparent">IT Hardware & Networking Support</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 mb-10 leading-relaxed font-normal">
                    Lodge complaints for internet disruptions, network cabling, computer hardware, printers, servers, and institutional software. Track technical progress in real time with end-to-end accountability.
                </p>

                <!-- QUICK ACTIONS -->
                <div class="flex flex-wrap items-center justify-center gap-4 mb-14">
                    <a href="{{ url('/admin/tickets/create') }}"
                       class="px-6 py-3.5 rounded-xl bg-uhs-500 hover:bg-uhs-600 text-white font-bold text-base shadow-lg shadow-uhs-500/30 transition transform hover:-translate-y-0.5 flex items-center gap-2.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lodge New Complaint</span>
                    </a>

                    <a href="#track"
                       class="px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-base border border-white/20 backdrop-blur-md transition flex items-center gap-2">
                        <svg class="w-5 h-5 text-uhs-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Track Existing Ticket</span>
                    </a>
                </div>

                <!-- QUICK STATS COUNTERS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto pt-6 border-t border-white/10 text-left">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <div class="text-2xl font-black text-uhs-300 font-display">{{ $stats['total_complaints'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">Total Complaints Lodged</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <div class="text-2xl font-black text-emerald-400 font-display">{{ $stats['resolved_complaints'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">Confirmed Resolved</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <div class="text-2xl font-black text-sky-300 font-display">{{ $stats['active_departments'] }}</div>
                        <div class="text-xs text-slate-400 mt-1">UHS Departments Active</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                        <div class="text-2xl font-black text-amber-300 font-display">&lt; 4 Hours</div>
                        <div class="text-xs text-slate-400 mt-1">Average Response Target</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE COMPLAINT TRACKER SECTION -->
    <section id="track" class="relative -mt-12 z-20 max-w-5xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-navy-950 font-display flex items-center gap-2">
                        <svg class="w-6 h-6 text-uhs-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Live Complaint Tracking System</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Enter your tracking ID to see current status, assigned technician, work notes, and real-time milestones.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400">Sample Ticket:</span>
                    <button onclick="fillSampleTicket('ITHN-2026-000001')" class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-uhs-700 border border-slate-200 transition">
                        ITHN-2026-000001
                    </button>
                </div>
            </div>

            <!-- SEARCH INPUT -->
            <form id="trackingForm" onsubmit="trackTicket(event)" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <input type="text"
                           id="trackingIdInput"
                           placeholder="Enter Tracking ID (e.g. ITHN-2026-000001)"
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-uhs-500 focus:border-uhs-500 focus:bg-white transition uppercase"
                           required>
                </div>
                <button type="submit"
                        id="trackButton"
                        class="px-6 py-3.5 bg-navy-950 hover:bg-uhs-700 text-white font-bold text-sm rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Check Status</span>
                </button>
            </form>

            <!-- TRACKING RESULT CONTAINER -->
            <div id="trackingResult" class="hidden mt-6 pt-6 border-t border-slate-200">
                <!-- Injected via JavaScript -->
            </div>
        </div>
    </section>

    <!-- KEY IT SERVICES CATALOGUE -->
    <section id="services" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-xs font-bold uppercase tracking-wider text-uhs-600 mb-2">Technical Services</h2>
            <p class="text-2xl sm:text-3xl font-extrabold text-navy-950 font-display">
                What Can the IT Department Help You With?
            </p>
            <p class="text-sm text-slate-500 mt-2">
                Our technicians provide on-site and remote technical support across all university faculties, auditoriums, and labs.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- 1. Internet & Networking -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">Internet & Fiber Connectivity</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Main gateway routing, departmental optical fiber links, high-speed bandwidth allocation, and DNS troubleshooting.
                </p>
                <div class="text-xs font-semibold text-uhs-600 flex items-center gap-1">
                    <span>Priority: High</span> • <span>SLA: 4h Resolution</span>
                </div>
            </div>

            <!-- 2. LAN & Cabling -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">LAN & Network Cabling</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Ethernet wall ports, RJ-45 connector crimping, patch panel punching, and departmental Cisco switches.
                </p>
                <div class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                    <span>Priority: High</span> • <span>SLA: 4h Resolution</span>
                </div>
            </div>

            <!-- 3. Wi-Fi & Eduroam -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">Campus Wi-Fi & Access Points</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Faculty wireless connectivity, student hostel coverage, dead zone mitigation, and Eduroam authentication.
                </p>
                <div class="text-xs font-semibold text-indigo-600 flex items-center gap-1">
                    <span>Priority: Medium</span> • <span>SLA: 8h Resolution</span>
                </div>
            </div>

            <!-- 4. PC Hardware & Laptops -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">Workstation & PC Hardware</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Desktop power supply faults, RAM upgrades, SSD installation, motherboard diagnostics, and monitor replacements.
                </p>
                <div class="text-xs font-semibold text-amber-600 flex items-center gap-1">
                    <span>Priority: Medium</span> • <span>SLA: 12h Resolution</span>
                </div>
            </div>

            <!-- 5. Network Printers & Scanners -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">Printers, Scanners & Spoolers</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Network shared printers, duplex copiers, paper jam mechanical clearing, print queue freeze, and IP configuration.
                </p>
                <div class="text-xs font-semibold text-rose-600 flex items-center gap-1">
                    <span>Priority: Medium</span> • <span>SLA: 8h Resolution</span>
                </div>
            </div>

            <!-- 6. Server Infrastructure -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-uhs-400 hover:shadow-md transition group">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy-950 mb-2">Servers, Storage & Domain</h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Institutional file shares, active directory authentication, examination database backup, and virtualization.
                </p>
                <div class="text-xs font-semibold text-purple-600 flex items-center gap-1">
                    <span>Priority: Critical</span> • <span>SLA: 2h Resolution</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 4-STEP WORKFLOW LIFECYCLE -->
    <section id="how-it-works" class="py-20 bg-slate-100 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-xs font-bold uppercase tracking-wider text-uhs-600 mb-2">Lifecycle Process</h2>
                <p class="text-2xl sm:text-3xl font-extrabold text-navy-950 font-display">
                    How Your Complaint Is Resolved
                </p>
                <p class="text-sm text-slate-500 mt-2">
                    Every ticket is tracked with complete transparency from your office desk to final confirmation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative">
                    <div class="w-10 h-10 rounded-full bg-uhs-900 text-white font-bold text-sm flex items-center justify-center mb-4">1</div>
                    <h3 class="text-base font-bold text-navy-950 mb-1">Lodge Ticket</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Submit complaint online. You receive a unique tracking ID (<code class="bg-slate-100 px-1 py-0.5 rounded text-uhs-700 font-mono">ITHN-2026-000001</code>) and SMS/email confirmation.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative">
                    <div class="w-10 h-10 rounded-full bg-uhs-600 text-white font-bold text-sm flex items-center justify-center mb-4">2</div>
                    <h3 class="text-base font-bold text-navy-950 mb-1">HOD Review & Assignment</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        IT HOD reviews urgency, sets SLA targets, and assigns the best-qualified hardware or networking technician.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative">
                    <div class="w-10 h-10 rounded-full bg-amber-500 text-white font-bold text-sm flex items-center justify-center mb-4">3</div>
                    <h3 class="text-base font-bold text-navy-950 mb-1">On-Site Investigation</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Technician visits your location, troubleshoots the equipment, records work notes, and marks work as completed.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm relative">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white font-bold text-sm flex items-center justify-center mb-4">4</div>
                    <h3 class="text-base font-bold text-navy-950 mb-1">Your Confirmation</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        You confirm whether the issue is resolved with a satisfaction rating. If the problem persists, reopen it with 1 click.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SELF-HELP GUIDES & KNOWLEDGE BASE -->
    <section id="knowledge" class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-uhs-600 mb-2">Troubleshooting Guides</h2>
                <p class="text-2xl sm:text-3xl font-extrabold text-navy-950 font-display">
                    Quick Self-Service Solutions
                </p>
            </div>
            <a href="{{ url('/admin/knowledge-articles') }}" class="text-sm font-semibold text-uhs-600 hover:text-uhs-800 flex items-center gap-1">
                <span>Browse All Guides</span>
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($articles as $article)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 hover:shadow-md transition">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                            {{ $article->category?->name ?? 'IT Guide' }}
                        </span>
                        <span class="text-xs text-slate-400">• {{ $article->views_count }} views</span>
                    </div>
                    <h3 class="text-lg font-bold text-navy-950 mb-2 hover:text-uhs-600 transition">
                        <a href="{{ url('/admin/knowledge-articles') }}">{{ $article->title }}</a>
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        {{ $article->summary ?: Str::limit($article->content, 120) }}
                    </p>
                    <a href="{{ url('/admin/knowledge-articles') }}" class="text-xs font-bold text-uhs-600 hover:underline inline-flex items-center gap-1">
                        <span>Read full guide</span>
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @empty
                <div class="col-span-2 text-center py-8 text-slate-400 italic">No troubleshooting guides published yet.</div>
            @endforelse
        </div>
    </section>

    <!-- EMERGENCY HELPLINE & FOOTER -->
    <footer id="helpline" class="bg-navy-950 text-slate-400 pt-16 pb-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-white/10">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3.5 mb-4">
                        <img src="{{ asset('images/uhs-logo.png') }}" 
                             alt="University of Health Sciences, Lahore" 
                             class="w-12 h-12 object-contain drop-shadow-sm flex-shrink-0"
                             style="max-width: 48px; max-height: 48px;">
                        <div>
                            <div class="text-white font-bold text-base font-display">University of Health Sciences, Lahore</div>
                            <div class="text-xs text-slate-400">Department of IT Hardware & Networking</div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                        Dedicated technical service desk managing campus-wide computing infrastructure, fiber network backbones, data center virtualization, exam software systems, and departmental connectivity.
                    </p>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Helpdesk Quick Links</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ url('/admin/tickets/create') }}" class="hover:text-white transition">Lodge a Complaint</a></li>
                        <li><a href="#track" class="hover:text-white transition">Track Ticket Status</a></li>
                        <li><a href="{{ url('/admin/knowledge-articles') }}" class="hover:text-white transition">Knowledge Base</a></li>
                        <li><a href="{{ url('/admin/login') }}" class="hover:text-white transition">Technician / Staff Login</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-semibold text-sm mb-4">Emergency IT Contacts</h4>
                    <ul class="space-y-2.5 text-xs">
                        <li class="flex items-start gap-2">
                            <span class="text-white font-semibold">Location:</span>
                            <span>Main Campus, Khayaban-e-Jamia Punjab, Lahore</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-white font-semibold">Helpline:</span>
                            <span class="text-emerald-400 font-mono">+92-42-99231304 (Ext: 304)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-white font-semibold">Email:</span>
                            <span class="text-uhs-300">it.support@uhs.edu.pk</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-white font-semibold">Hours:</span>
                            <span>Mon - Fri (8:00 AM - 4:00 PM)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div>
                    © 2026 University of Health Sciences, Lahore. All Rights Reserved. IT Service Desk Portal.
                </div>
                <div class="flex items-center gap-4">
                    <span>PHP 8.4 • Laravel 13 • Filament v5 • Livewire</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE JAVASCRIPT FOR LIVE TICKET TRACKING -->
    <script>
        function fillSampleTicket(id) {
            const input = document.getElementById('trackingIdInput');
            input.value = id;
            document.getElementById('trackingForm').dispatchEvent(new Event('submit'));
        }

        async function trackTicket(event) {
            event.preventDefault();
            const input = document.getElementById('trackingIdInput');
            const btn = document.getElementById('trackButton');
            const container = document.getElementById('trackingResult');
            const trackingId = input.value.trim();

            if (!trackingId) return;

            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Searching...`;

            try {
                const response = await fetch("{{ route('complaint.track') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ tracking_id: trackingId })
                });

                const data = await response.json();
                container.classList.remove('hidden');

                if (response.ok && data.found) {
                    const t = data.ticket;

                    let statusBadgeClass = 'bg-blue-100 text-blue-800';
                    if (t.status === 'closed') statusBadgeClass = 'bg-emerald-100 text-emerald-800';
                    else if (t.status === 'reopened') statusBadgeClass = 'bg-rose-100 text-rose-800';
                    else if (t.status === 'awaiting_confirmation') statusBadgeClass = 'bg-purple-100 text-purple-800';
                    else if (t.status === 'in_progress') statusBadgeClass = 'bg-amber-100 text-amber-800';

                    let timelineHtml = '';
                    if (t.timeline && t.timeline.length > 0) {
                        timelineHtml = `
                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Milestone Activity Log</h4>
                                <div class="relative pl-5 border-l-2 border-uhs-500 space-y-4">
                                    ${t.timeline.map(item => `
                                        <div class="relative">
                                            <div class="absolute -left-[27px] top-1 w-3 h-3 rounded-full border-2 border-white bg-uhs-600"></div>
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="font-bold text-slate-800">${item.action}</span>
                                                <span class="text-slate-400">${item.date} (${item.time_ago})</span>
                                            </div>
                                            <div class="text-xs text-slate-600 mt-0.5">By <strong>${item.actor}</strong></div>
                                            ${item.comment ? `<div class="mt-1 text-xs bg-slate-50 p-2 rounded border border-slate-200 text-slate-700">${item.comment}</div>` : ''}
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }

                    container.innerHTML = `
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                                <div>
                                    <span class="font-mono text-sm font-bold text-uhs-700">${t.tracking_id}</span>
                                    <h3 class="text-lg font-extrabold text-navy-950 mt-0.5">${t.title}</h3>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold ${statusBadgeClass} uppercase tracking-wider">
                                    ${t.status_label}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs mt-4 pt-4 border-t border-slate-200">
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Department</span>
                                    <span class="font-semibold text-slate-800">${t.department}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Category</span>
                                    <span class="font-semibold text-slate-800">${t.category}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Priority</span>
                                    <span class="font-semibold text-slate-800">${t.priority}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Assigned Technician</span>
                                    <span class="font-semibold text-slate-800">${t.technician}</span>
                                </div>
                            </div>

                            ${t.resolution_summary ? `
                                <div class="mt-4 p-3 bg-emerald-50 rounded-lg border border-emerald-200 text-xs text-emerald-900">
                                    <strong>Resolution Summary:</strong> ${t.resolution_summary}
                                </div>
                            ` : ''}

                            ${timelineHtml}

                            <div class="mt-6 flex items-center justify-between pt-4 border-t border-slate-200">
                                <span class="text-xs text-slate-400">Target Resolution: ${t.sla_resolution_deadline}</span>
                                <a href="${t.portal_url}" class="text-xs font-bold text-uhs-700 hover:text-uhs-900 flex items-center gap-1">
                                    <span>Open In Portal & Confirm/Reply</span>
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>${data.message || 'Complaint not found.'}</span>
                        </div>
                    `;
                }
            } catch (err) {
                container.innerHTML = `
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        Unable to connect to tracking server. Please try again.
                    </div>
                `;
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg><span>Check Status</span>`;
            }
        }

        // ==========================================
        // INTERACTIVE COSMIC UNIVERSE & STARRY CLOUD
        // ==========================================
        (function() {
            const canvas = document.getElementById('cosmic-stars-canvas');
            const hero = document.getElementById('hero-cosmic-universe');
            const nebula = document.getElementById('cosmic-nebula-glow');
            if (!canvas || !hero) return;

            const ctx = canvas.getContext('2d');
            let width = 0, height = 0;
            let particles = [];
            let stardust = [];
            let mouse = { x: -1000, y: -1000, targetX: -1000, targetY: -1000, isHovered: false };
            let isVisible = true;

            const starColors = [
                'rgba(255, 255, 255, ',   // Brilliant star white
                'rgba(56, 189, 248, ',   // Celestial cyan
                'rgba(52, 211, 153, ',   // Emerald pulse
                'rgba(167, 139, 250, ',  // Soft nebula purple
                'rgba(191, 219, 254, ',  // Deep space blue
            ];

            function resize() {
                const rect = hero.getBoundingClientRect();
                width = canvas.width = rect.width;
                height = canvas.height = rect.height;
                initUniverse();
            }

            class Star {
                constructor() {
                    this.reset(true);
                }

                reset(initial = false) {
                    this.x = initial ? Math.random() * (width || 1200) : (Math.random() < 0.5 ? 0 : width);
                    this.y = initial ? Math.random() * (height || 600) : Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.45;
                    this.vy = (Math.random() - 0.5) * 0.45;
                    this.baseRadius = Math.random() * 1.8 + 0.6;
                    this.radius = this.baseRadius;
                    this.color = starColors[Math.floor(Math.random() * starColors.length)];
                    this.alpha = Math.random() * 0.6 + 0.3;
                    this.alphaSpeed = (Math.random() * 0.012 + 0.005) * (Math.random() < 0.5 ? 1 : -1);
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    // Wrap edges smoothly
                    if (this.x < -10) this.x = width + 10;
                    if (this.x > width + 10) this.x = -10;
                    if (this.y < -10) this.y = height + 10;
                    if (this.y > height + 10) this.y = -10;

                    // Twinkle shimmer
                    this.alpha += this.alphaSpeed;
                    if (this.alpha >= 0.95 || this.alpha <= 0.2) {
                        this.alphaSpeed *= -1;
                    }

                    // Mouse gravitational swirl & expansion
                    if (mouse.isHovered) {
                        const dx = mouse.x - this.x;
                        const dy = mouse.y - this.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        const maxDist = 200;

                        if (dist < maxDist && dist > 0) {
                            const pull = (1 - dist / maxDist);
                            // Gentle celestial gravity
                            this.x += (dx / dist) * pull * 0.9;
                            this.y += (dy / dist) * pull * 0.9;
                            this.radius = this.baseRadius + pull * 1.5;
                        } else {
                            this.radius = this.baseRadius;
                        }
                    } else {
                        this.radius = this.baseRadius;
                    }
                }

                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fillStyle = this.color + this.alpha + ')';
                    ctx.shadowBlur = this.radius * 4;
                    ctx.shadowColor = this.color + '0.8)';
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }
            }

            class StardustCloud {
                constructor(x, y) {
                    this.x = x + (Math.random() - 0.5) * 20;
                    this.y = y + (Math.random() - 0.5) * 20;
                    const angle = Math.random() * Math.PI * 2;
                    const speed = Math.random() * 2.2 + 0.5;
                    this.vx = Math.cos(angle) * speed;
                    this.vy = Math.sin(angle) * speed;
                    this.radius = Math.random() * 2.5 + 1;
                    this.color = starColors[Math.floor(Math.random() * starColors.length)];
                    this.life = 1;
                    this.decay = Math.random() * 0.02 + 0.015;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    this.vx *= 0.96;
                    this.vy *= 0.96;
                    this.life -= this.decay;
                }

                draw() {
                    if (this.life <= 0) return;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius * this.life, 0, Math.PI * 2);
                    ctx.fillStyle = this.color + this.life + ')';
                    ctx.shadowBlur = 8;
                    ctx.shadowColor = this.color + '1)';
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }
            }

            function initUniverse() {
                particles = [];
                const starCount = Math.min(130, Math.max(50, Math.floor((width * height) / 9000)));
                for (let i = 0; i < starCount; i++) {
                    particles.push(new Star());
                }
            }

            function drawConstellations() {
                const lineDist = 95;
                const mouseConnectDist = 160;

                // Connect star to star
                for (let i = 0; i < particles.length; i++) {
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < lineDist) {
                            const alpha = (1 - dist / lineDist) * 0.22;
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(125, 211, 252, ${alpha})`;
                            ctx.lineWidth = 0.65;
                            ctx.stroke();
                        }
                    }

                    // Interactive lines to user cursor
                    if (mouse.isHovered) {
                        const dx = particles[i].x - mouse.x;
                        const dy = particles[i].y - mouse.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < mouseConnectDist) {
                            const alpha = (1 - dist / mouseConnectDist) * 0.55;
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(mouse.x, mouse.y);
                            ctx.strokeStyle = `rgba(52, 211, 153, ${alpha})`;
                            ctx.lineWidth = 0.9;
                            ctx.stroke();
                        }
                    }
                }
            }

            function animate() {
                if (!isVisible) {
                    requestAnimationFrame(animate);
                    return;
                }

                ctx.clearRect(0, 0, width, height);

                // Smooth mouse spring physics
                mouse.x += (mouse.targetX - mouse.x) * 0.12;
                mouse.y += (mouse.targetY - mouse.y) * 0.12;

                // Move cosmic nebula glow to follow mouse
                if (nebula && mouse.isHovered) {
                    nebula.style.transform = `translate(${mouse.x}px, ${mouse.y}px) translate(-50%, -50%)`;
                    nebula.style.opacity = '1';
                }

                drawConstellations();

                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }

                for (let i = stardust.length - 1; i >= 0; i--) {
                    stardust[i].update();
                    stardust[i].draw();
                    if (stardust[i].life <= 0) {
                        stardust.splice(i, 1);
                    }
                }

                requestAnimationFrame(animate);
            }

            hero.addEventListener('mousemove', (e) => {
                const rect = hero.getBoundingClientRect();
                mouse.targetX = e.clientX - rect.left;
                mouse.targetY = e.clientY - rect.top;
                mouse.isHovered = true;

                // Ethereal stardust trail
                if (Math.random() < 0.25) {
                    stardust.push(new StardustCloud(mouse.targetX, mouse.targetY));
                }
            });

            hero.addEventListener('mouseenter', () => {
                mouse.isHovered = true;
            });

            hero.addEventListener('mouseleave', () => {
                mouse.isHovered = false;
                mouse.targetX = -1000;
                mouse.targetY = -1000;
                if (nebula) nebula.style.opacity = '0';
            });

            // Supernova burst on click
            hero.addEventListener('click', (e) => {
                const rect = hero.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const clickY = e.clientY - rect.top;
                for (let i = 0; i < 28; i++) {
                    stardust.push(new StardustCloud(clickX, clickY));
                }
            });

            // Pause when offscreen
            const observer = new IntersectionObserver((entries) => {
                isVisible = entries[0].isIntersecting;
            }, { threshold: 0.05 });
            observer.observe(hero);

            window.addEventListener('resize', resize);
            resize();
            requestAnimationFrame(animate);
        })();
    </script>
</body>
</html>
