<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'By Mex Admin') }} | Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root {
            --primary: #D62828;
            --navy: #0D1B2A;
            --navy-light: #1B2A41;
            --gold: #F4A261;
            --bg-body: #F8FAFC;
            --border: #E2E8F0;
            --text-main: #334155;
            --text-muted: #64748B;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-body); color: var(--text-main); display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 280px; background-color: var(--navy); color: white; display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 0; height: 100vh; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 1rem; }
        .sidebar-header img { width: 40px; border-radius: 8px; }
        .sidebar-header h2 { font-family: 'Montserrat', sans-serif; font-size: 1.2rem; font-weight: 800; color: white; }
        .sidebar-header span { color: var(--gold); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .sidebar-nav { flex: 1; padding: 1.5rem 0; }
        .nav-item { padding: 0.85rem 1.5rem; display: flex; align-items: center; gap: 1rem; color: #cbd5e1; text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: all 0.3s; border-left: 4px solid transparent; }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.05); color: white; border-left-color: var(--primary); }
        .nav-item i { width: 20px; text-align: center; color: var(--gold); font-size: 1.1rem; }
        
        .sidebar-footer { padding: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); }
        .user-info { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
        .user-avatar { width: 40px; height: 40px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; }
        .user-details h4 { font-size: 0.9rem; color: white; }
        .user-details p { font-size: 0.75rem; color: #94a3b8; }
        
        .btn-logout { width: 100%; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: white; padding: 0.5rem; border-radius: 6px; cursor: pointer; transition: background 0.3s; font-size: 0.85rem; }
        .btn-logout:hover { background: rgba(214,40,40,0.2); border-color: var(--primary); color: var(--primary); }

        /* Main Content */
        .main-content { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        
        .topbar { background: white; padding: 1rem 2rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .topbar h1 { font-family: 'Montserrat', sans-serif; font-size: 1.5rem; color: var(--navy); font-weight: 700; }
        .topbar-actions { display: flex; gap: 1rem; align-items: center; }
        .btn-site { background: white; color: var(--navy); padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; border: 1px solid var(--border); transition: all 0.2s; }
        .btn-site:hover { background: var(--bg-body); border-color: var(--navy); }

        .content-area { padding: 2rem; overflow-y: auto; }
        
        /* Dashboard UI Elements */
        .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border: 1px solid var(--border); overflow: hidden; margin-bottom: 2rem; }
        .card-header { padding: 1.5rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 1.1rem; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: 0.5rem; }
        .card-body { padding: 1.5rem; }
        
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: #F8FAFC; padding: 1rem; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; letter-spacing: 0.5px; border-bottom: 1px solid var(--border); }
        .data-table td { padding: 1rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; color: var(--text-main); vertical-align: middle; }
        .data-table tr:hover { background: #F1F5F9; }
        .data-table tr:last-child td { border-bottom: none; }
        
        .badge { padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        
        .btn-action { background: var(--navy); color: white; padding: 0.4rem 0.8rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; border: none; }
        .btn-action:hover { background: var(--primary); }

        /* Dropdowns & Notifications */
        .notification-bell { position: relative; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; justify-content: center; color: var(--navy); font-size: 1.2rem; border-radius: 8px; transition: background 0.2s; }
        .notification-bell:hover { background: var(--bg-body); }
        .notification-badge-count { position: absolute; top: 0; right: -2px; background: var(--primary); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.65rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid white; }
        
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; right: 0; top: calc(100% + 8px); background-color: white; min-width: 340px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border-radius: 12px; border: 1px solid var(--border); z-index: 200; overflow: hidden; }
        .dropdown.show .dropdown-content { display: block; animation: slideDown 0.2s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .dropdown-header { padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); font-weight: 700; font-size: 0.9rem; color: var(--navy); }
        
        .notification-item { padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border); display: flex; gap: 0.75rem; align-items: flex-start; text-decoration: none; color: var(--text-main); transition: background 0.15s; }
        .notification-item:hover { background: #f8fafc; }
        .notification-item:last-child { border-bottom: none; }
        .notif-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
        .notif-empty { padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem; }
        
        /* Context Menu (3 dots) */
        .context-menu-btn { background: transparent; border: 1px solid var(--border); color: var(--text-muted); width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .context-menu-btn:hover { background: var(--bg-body); color: var(--navy); border-color: var(--navy); }
        .context-dropdown { min-width: 240px; }
        .context-item { padding: 0.65rem 1rem; display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: background 0.15s; }
        .context-item:hover { background: #f1f5f9; }
        .context-item i { color: var(--text-muted); width: 18px; text-align: center; font-size: 0.9rem; }
        .context-divider { border-top: 1px solid var(--border); margin: 0.25rem 0; }

        /* Leaflet overrides */
        .leaflet-container { border-radius: 12px; font-family: 'Inter', sans-serif; }
        .map-popup { font-family: 'Inter', sans-serif; }
        .map-popup h4 { font-size: 0.85rem; font-weight: 700; color: var(--navy); margin-bottom: 0.25rem; }
        .map-popup p { font-size: 0.75rem; color: var(--text-muted); margin: 0; }
        .map-popup .price { color: #166534; font-weight: 700; font-size: 0.9rem; }

        /* Progress bar */
        .progress-bar { width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; margin-top: 0.5rem; }
        .progress-fill { height: 100%; border-radius: 3px; transition: width 0.8s ease; }

        /* ================================================================
           Responsive Admin Layout
           ================================================================ */
        /* Utility classes for grids */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }
        .map-chart-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }

        .mobile-toggle { display: none; background: none; border: none; color: var(--navy); font-size: 1.5rem; cursor: pointer; }

        @media (max-width: 1024px) {
            .map-chart-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            /* Sidebar becomes a drawer or hides behind a toggle */
            .sidebar { 
                position: fixed; 
                left: -280px; 
                top: 0; 
                z-index: 1000; 
                transition: left 0.3s ease; 
                box-shadow: 0 0 20px rgba(0,0,0,0.5);
            }
            .sidebar.active { left: 0; }
            
            .mobile-toggle { display: block; margin-right: 1rem; }
            
            .content-area { padding: 1rem; }
            
            .topbar { padding: 1rem; flex-wrap: wrap; gap: 1rem; }
            .topbar h1 { font-size: 1.2rem; }
            
            .card-header { padding: 1rem; flex-direction: column; align-items: flex-start; gap: 1rem; }
            .btn-action { align-self: flex-start; }
            
            /* Make tables horizontally scrollable */
            .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        }
    </style>
    @yield('extra-css')
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                <img src="{{ asset('img/logobymex.jpeg') }}" alt="By Mex">
                <div>
                    <h2>By Mex</h2>
                    <span>Admin Panel</span>
                </div>
            </div>
            <button class="mobile-toggle" style="color: white; margin: 0;" onclick="document.querySelector('.sidebar').classList.remove('active')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('admin.tours.index') }}" class="nav-item {{ request()->routeIs('admin.tours.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bus"></i> Catálogo de Viajes
            </a>
            <a href="{{ route('admin.clients.index') }}" class="nav-item {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Clientes
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Configuración
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
                <div class="user-details">
                    <h4>{{ Auth::user()->name ?? 'Administrador' }}</h4>
                    <p>{{ Auth::user()->email ?? 'admin@bymex.com' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar">
            <div style="display: flex; align-items: center;">
                <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1>@yield('header-title', 'Dashboard')</h1>
            </div>
            <div class="topbar-actions">
                <!-- Real Notifications from DB -->
                @php $notifs = $notifications ?? []; @endphp
                <div class="dropdown" id="notifDropdown">
                    <div class="notification-bell" onclick="toggleDropdown('notifDropdown')">
                        <i class="fa-regular fa-bell"></i>
                        @if(count($notifs) > 0)
                            <span class="notification-badge-count">{{ count($notifs) }}</span>
                        @endif
                    </div>
                    <div class="dropdown-content">
                        <div class="dropdown-header"><i class="fa-regular fa-bell"></i> Notificaciones ({{ count($notifs) }})</div>
                        @forelse($notifs as $notif)
                            <a href="{{ $notif['link'] }}" class="notification-item">
                                <div class="notif-icon" style="background: {{ $notif['color'] }}20; color: {{ $notif['color'] }};"><i class="{{ $notif['icon'] }}"></i></div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.85rem;">{{ $notif['title'] }}</div>
                                    <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $notif['desc'] }}</div>
                                    <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">{{ $notif['time'] }}</div>
                                </div>
                                @if(isset($notif['whatsapp_link']) && $notif['whatsapp_link'])
                                <object><a href="{{ $notif['whatsapp_link'] }}" target="_blank" style="color: #25D366; font-size: 1.2rem; align-self: center; margin-left: 0.5rem;" title="Notificar por WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a></object>
                                @endif
                            </a>
                        @empty
                            <div class="notif-empty"><i class="fa-regular fa-circle-check" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; color: #166534;"></i>Todo en orden, sin alertas pendientes.</div>
                        @endforelse
                    </div>
                </div>
                <a href="{{ url('/') }}" class="btn-site" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Sitio Web</a>
            </div>
        </header>
        
        <main class="content-area">
            @if(session('success'))
                <div style="background: #dcfce7; border-left: 4px solid #166534; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px; color: #166534; font-weight: 500;">
                    <i class="fa-solid fa-check-circle" style="margin-right: .5rem;"></i> {{ session('success') }}
                </div>
            @endif
            
            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Leaflet.js -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <script>
        function toggleDropdown(id) {
            // Close all other dropdowns first
            document.querySelectorAll('.dropdown.show').forEach(function(d) {
                if (d.id !== id) d.classList.remove('show');
            });
            document.getElementById(id).classList.toggle('show');

            // If opening notification dropdown, mark as read
            if (id === 'notifDropdown' && document.getElementById(id).classList.contains('show')) {
                markNotificationsRead();
            }
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown.show').forEach(function(d) {
                    d.classList.remove('show');
                });
            }
        });

        // --- REAL-TIME NOTIFICATIONS POLLING ---
        // Base64 short "ping" sound to avoid needing external audio files
        const pingSound = new Audio("data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU"+Array(200).join("A"));
        
        // A simple synthesizer function for a clean notification bell sound
        function playNotificationSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime); // A5
                osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.5);
                gain.gain.setValueAtTime(0.5, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.5);
            } catch (e) {
                console.log('Audio not supported or blocked');
            }
        }

        let lastNotifCount = {{ isset($notifications) ? count($notifications) : 0 }};

        function fetchNotifications() {
            fetch('{{ route("admin.notifications.api") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.count > lastNotifCount) {
                    playNotificationSound();
                    updateNotificationUI(data);
                }
                lastNotifCount = data.count;
            })
            .catch(err => console.error("Error fetching notifications:", err));
        }

        function updateNotificationUI(data) {
            const bellContainer = document.querySelector('.notification-bell');
            
            // Update or create badge
            let badge = document.querySelector('.notification-badge-count');
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'notification-badge-count';
                bellContainer.appendChild(badge);
            }
            badge.innerText = data.count;

            // Update dropdown header
            const header = document.querySelector('.dropdown-header');
            if (header) {
                header.innerHTML = `<i class="fa-regular fa-bell"></i> Notificaciones (${data.count})`;
            }

            // Rebuild list
            const dropdownContent = document.querySelector('.dropdown-content');
            // Keep header, remove the rest
            Array.from(dropdownContent.children).forEach(child => {
                if (!child.classList.contains('dropdown-header')) {
                    child.remove();
                }
            });

            if (data.items.length === 0) {
                dropdownContent.innerHTML += `<div class="notif-empty"><i class="fa-regular fa-circle-check" style="font-size: 1.5rem; margin-bottom: 0.5rem; display: block; color: #166534;"></i>Todo en orden, sin alertas pendientes.</div>`;
            } else {
                data.items.forEach(item => {
                    let color = item.type === 'reservation_new' ? '#1e40af' : '#64748b';
                    let icon = item.type === 'reservation_new' ? 'fa-solid fa-ticket' : 'fa-solid fa-bell';
                    
                    let html = `
                        <a href="${item.link}" class="notification-item" style="background: #f0f9ff;">
                            <div class="notif-icon" style="background: ${color}20; color: ${color};"><i class="${icon}"></i></div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.85rem;">${item.title}</div>
                                <div style="font-size: 0.78rem; color: var(--text-muted);">${item.message}</div>
                                <div style="font-size: 0.7rem; color: #94a3b8; margin-top: 2px;">${item.time}</div>
                            </div>
                    `;
                    
                    if (item.whatsapp_link) {
                        html += `
                            <object><a href="${item.whatsapp_link}" target="_blank" style="color: #25D366; font-size: 1.2rem; align-self: center; margin-left: 0.5rem;" title="Notificar por WhatsApp">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a></object>
                        `;
                    }
                    
                    html += `</a>`;
                    dropdownContent.innerHTML += html;
                });
            }
        }

        function markNotificationsRead() {
            fetch('{{ route("admin.notifications.read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                const badge = document.querySelector('.notification-badge-count');
                if (badge) badge.remove();
                lastNotifCount = 0;
            });
        }

        // Poll every 10 seconds
        setInterval(fetchNotifications, 10000);
    </script>
    @yield('extra-js')
</body>
</html>
