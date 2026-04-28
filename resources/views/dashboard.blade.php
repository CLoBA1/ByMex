<x-app-layout>
    @section('header-title', 'Resumen General')

    @php
        $totalTours = count($tours);
        $totalSeats = $tours->sum('total_seats');
        $totalOccupied = $tours->sum('seats_count');
        $totalReservations = $tours->sum('reservations_count');
        $occupancyPct = $totalSeats > 0 ? round(($totalOccupied / $totalSeats) * 100) : 0;

        // Coordinates map for known Mexican destinations
        $coordsMap = [
            'Xicotepec' => [20.2746, -97.9639],
            'Huauchinango' => [20.1764, -98.0525],
            'Cascadas' => [20.2746, -97.9639],
            'Tolantongo' => [20.6500, -99.0000],
            'Hidalgo' => [20.1167, -98.7333],
            'Real del Monte' => [20.1319, -98.6750],
            'Senderos' => [19.2833, -96.9167],
            'Café' => [19.2833, -96.9167],
            'Xalapa' => [19.5438, -96.9102],
            'Santuarios' => [19.5978, -100.2560],
            'Juquila' => [16.2350, -97.2900],
            'Oaxaca' => [17.0732, -96.7266],
            'Tuxtlas' => [18.4500, -95.2000],
            'Catemaco' => [18.4217, -95.1133],
            'Puebla' => [19.0414, -98.2063],
            'Tlaxcala' => [19.3139, -98.2400],
            'Acapulco' => [16.8531, -99.8237],
            'Guerrero' => [17.4392, -99.5451],
            'Cuernavaca' => [18.9242, -99.2216],
            'México' => [19.4326, -99.1332],
        ];

        $tourCoords = [];
        foreach ($tours as $tour) {
            $lat = 19.4326; $lng = -99.1332; // Default: CDMX
            foreach ($coordsMap as $key => $coords) {
                if (stripos($tour->title, $key) !== false || stripos($tour->destination, $key) !== false) {
                    $lat = $coords[0]; $lng = $coords[1];
                    break;
                }
            }
            $tourCoords[] = [
                'id' => $tour->id,
                'title' => $tour->title,
                'destination' => $tour->destination,
                'lat' => $lat,
                'lng' => $lng,
                'price' => $tour->price,
                'date' => \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d M Y'),
                'image' => $tour->image ? asset($tour->image) : null,
                'occupied' => $tour->seats_count,
                'total' => $tour->total_seats,
                'url' => route('admin.tours.show', $tour->id),
            ];
        }
    @endphp

    <!-- KPI Cards -->
    <div class="kpi-grid">
        
        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Reservas Totales</p>
                        <h3 style="font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1;">{{ $totalReservations }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(214,40,40,0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Ocupación Promedio</p>
                        <h3 style="font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1;">{{ $occupancyPct }}%</h3>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $occupancyPct }}%; background: {{ $occupancyPct > 70 ? '#166534' : ($occupancyPct > 30 ? '#F4A261' : '#D62828') }};"></div>
                        </div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(244,162,97,0.1); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Ingresos Cobrados</p>
                        <h3 style="font-size: 2rem; font-weight: 800; color: #166534; line-height: 1;">${{ number_format($totalPaidRevenue, 0) }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(22,101,52,0.08); color: #166534; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-peso-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-bottom: 0;">
            <div class="card-body" style="padding: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <p style="font-size: 0.8rem; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Clientes Registrados</p>
                        <h3 style="font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1;">{{ $totalClients }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(13,27,42,0.06); color: var(--navy); display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Map + Occupancy Chart Row -->
    <div class="map-chart-grid">
        <!-- Leaflet Map -->
        <div class="card" style="margin-bottom: 0; overflow: hidden;">
            <div class="card-header" style="padding: 1rem 1.5rem;">
                <h2 class="card-title"><i class="fa-solid fa-map-location-dot"></i> Mapa de Destinos</h2>
            </div>
            <div id="tourMap" style="height: 320px; z-index: 1;"></div>
        </div>

        <!-- Chart.js Doughnut -->
        <div class="card" style="margin-bottom: 0;">
            <div class="card-header" style="padding: 1rem 1.5rem;">
                <h2 class="card-title"><i class="fa-solid fa-chart-pie"></i> Ocupación General</h2>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem;">
                <canvas id="occupancyChart" style="max-width: 220px; max-height: 220px;"></canvas>
                <div style="margin-top: 1rem; text-align: center;">
                    <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $totalOccupied }} ocupados de {{ $totalSeats }} totales</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tours Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-bus"></i> Catálogo de Próximos Viajes</h2>
            <a href="{{ route('admin.tours.create') }}" class="btn-action"><i class="fa-solid fa-plus"></i> Nuevo Tour</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tour / Destino</th>
                        <th>Fecha de Salida</th>
                        <th>Precio</th>
                        <th style="text-align:center;">Ocupación</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tours as $tour)
                        @php
                            $pct = $tour->total_seats > 0 ? round(($tour->seats_count / $tour->total_seats) * 100) : 0;
                            if ($pct >= 90) { $badgeClass = 'badge-orange'; $statusText = 'Casi Lleno'; }
                            elseif ($pct >= 50) { $badgeClass = 'badge-blue'; $statusText = 'En Venta'; }
                            elseif ($pct > 0) { $badgeClass = 'badge-green'; $statusText = 'Disponible'; }
                            else { $badgeClass = 'badge-green'; $statusText = 'Disponible'; }
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    @if($tour->image)
                                        <img src="{{ asset($tour->image) }}" alt="{{ $tour->title }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); flex-shrink: 0;">
                                    @else
                                        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--bg-body); display: flex; align-items: center; justify-content: center; color: var(--text-muted); border: 2px solid var(--border); flex-shrink: 0;"><i class="fa-solid fa-bus"></i></div>
                                    @endif
                                    <div>
                                        <div style="font-weight: 700; color: var(--navy); font-size: 0.95rem;">{{ $tour->title }}</div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> {{ $tour->destination }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600;">{{ \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d M Y') }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ \Carbon\Carbon::parse($tour->departure_date)->format('H:i') }} hrs</div>
                            </td>
                            <td style="font-weight: 700; color: var(--navy);">${{ number_format($tour->price, 0) }}</td>
                            <td style="text-align:center;">
                                <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-top: 0.25rem;">{{ $tour->seats_count }} / {{ $tour->total_seats }} asnts</div>
                            </td>
                            <td style="text-align:right;">
                                <div class="dropdown" id="ctx{{ $tour->id }}">
                                    <button class="context-menu-btn" onclick="toggleDropdown('ctx{{ $tour->id }}')">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-content context-dropdown">
                                        <a href="{{ route('admin.tours.show', $tour->id) }}" class="context-item">
                                            <i class="fa-solid fa-eye"></i> Ver Reservas
                                        </a>
                                        <a href="{{ route('admin.tours.edit', $tour->id) }}" class="context-item">
                                            <i class="fa-solid fa-pen-to-square"></i> Editar Tour
                                        </a>
                                        <div class="context-divider"></div>
                                        <a href="https://wa.me/?text={{ urlencode('¡Hola! Les recordamos que el viaje "' . $tour->title . '" sale el ' . \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d \d\e F') . '. ¡Los esperamos!') }}" target="_blank" class="context-item">
                                            <i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> Enviar WhatsApp Grupal
                                        </a>
                                        <a href="{{ route('tours.show', $tour->id) }}" target="_blank" class="context-item">
                                            <i class="fa-solid fa-globe"></i> Ver Página Pública
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class="fa-solid fa-bus" style="font-size: 2rem; color: var(--border); margin-bottom: 1rem; display: block;"></i>
                                No hay tours configurados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @section('extra-js')
    <script>
        // ===== LEAFLET MAP =====
        var map = L.map('tourMap', {
            scrollWheelZoom: false,
            zoomControl: true,
        }).setView([19.5, -98.5], 6);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a>',
            maxZoom: 19
        }).addTo(map);

        var tours = @json($tourCoords);

        var customIcon = L.divIcon({
            html: '<div style="background: #D62828; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-bus" style="color: white; font-size: 0.6rem;"></i></div>',
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -18]
        });

        tours.forEach(function(t) {
            var imgHtml = t.image 
                ? '<img src="' + t.image + '" style="width:100%; height:80px; object-fit:cover; border-radius:6px; margin-bottom:8px;">' 
                : '';
            var popup = '<div class="map-popup">' + imgHtml +
                '<h4>' + t.title + '</h4>' +
                '<p><i class="fa-solid fa-location-dot"></i> ' + t.destination + '</p>' +
                '<p><i class="fa-regular fa-calendar"></i> ' + t.date + '</p>' +
                '<p class="price" style="margin-top:4px;">$' + Number(t.price).toLocaleString() + ' MXN</p>' +
                '<p style="margin-top:4px;">' + t.occupied + '/' + t.total + ' asientos</p>' +
                '<a href="' + t.url + '" style="display:inline-block; margin-top:6px; background:#0D1B2A; color:white; padding:4px 10px; border-radius:4px; text-decoration:none; font-size:0.75rem; font-weight:600;">Ver Reservas →</a>' +
                '</div>';

            L.marker([t.lat, t.lng], { icon: customIcon })
                .addTo(map)
                .bindPopup(popup, { maxWidth: 220 });
        });

        // Fit bounds to show all markers
        if (tours.length > 0) {
            var bounds = L.latLngBounds(tours.map(function(t) { return [t.lat, t.lng]; }));
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 8 });
        }

        // ===== CHART.JS DOUGHNUT =====
        var ctx = document.getElementById('occupancyChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Ocupados', 'Disponibles'],
                datasets: [{
                    data: [{{ $totalOccupied }}, {{ $totalSeats - $totalOccupied }}],
                    backgroundColor: ['#0D1B2A', '#E2E8F0'],
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                cutout: '72%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Inter', size: 12, weight: '500' },
                            padding: 16,
                            usePointStyle: true,
                            pointStyleWidth: 10,
                        }
                    },
                }
            }
        });
    </script>
    @endsection

</x-app-layout>
