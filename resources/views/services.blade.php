@extends('layouts.public')

@section('title', 'Nuestros Servicios')

@section('content')
    <!-- Header -->
    <div style="background: var(--navy); padding: 6rem 0 3rem; text-align:center; color: white;">
        <div class="container">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Nuestros Servicios</h1>
            <p style="color: var(--slate-400); max-width: 600px; margin: 0 auto;">Más que viajes, creamos experiencias a tu medida. Conoce todo lo que podemos ofrecerte.</p>
        </div>
    </div>

    <!-- Nuestros Servicios -->
    <section class="section-pad" id="servicios" style="background: var(--navy);">
        <div class="container" style="max-width: 1200px;">
            @php
                $waNumber = '527441295026';
                $servicios = [
                    [
                        'icon' => 'fa-solid fa-map-location-dot',
                        'color' => '#d62828',
                        'title' => 'Paquetes Nacionales',
                        'desc' => 'Descubre los destinos más espectaculares de México: playas, pueblos mágicos, ciudades coloniales y aventuras naturales con todo incluido.',
                        'wa_msg' => 'Hola, me interesa información sobre paquetes nacionales.',
                    ],
                    [
                        'icon' => 'fa-solid fa-earth-americas',
                        'color' => '#0fa3b1',
                        'title' => 'Paquetes Internacionales',
                        'desc' => 'Viaja más allá de las fronteras. Te organizamos paquetes a destinos internacionales con vuelos, hospedaje y actividades coordinadas.',
                        'wa_msg' => 'Hola, me interesa información sobre paquetes internacionales.',
                    ],
                    [
                        'icon' => 'fa-solid fa-wand-magic-sparkles',
                        'color' => '#f4a523',
                        'title' => 'Paquetes Especiales',
                        'desc' => 'Viajes 100% personalizados. Tú eliges destino, fechas, número de personas, duración, hospedaje, lugares a visitar y tipo de traslado. Nosotros lo hacemos realidad.',
                        'wa_msg' => 'Hola, quiero cotizar un paquete especial personalizado.',
                    ],
                    [
                        'icon' => 'fa-solid fa-hotel',
                        'color' => '#6366f1',
                        'title' => 'Reservación de Hoteles',
                        'desc' => 'Encuentra el hospedaje ideal para tu viaje. Te conseguimos las mejores tarifas en hoteles nacionales e internacionales.',
                        'wa_msg' => 'Hola, necesito cotizar una reservación de hotel.',
                    ],
                    [
                        'icon' => 'fa-solid fa-plane-departure',
                        'color' => '#166534',
                        'title' => 'Reservación de Vuelos',
                        'desc' => 'Vuelos nacionales e internacionales al mejor precio. Compara opciones y reserva con la confianza de tener asistencia personalizada.',
                        'wa_msg' => 'Hola, necesito cotizar un vuelo.',
                    ],
                ];
            @endphp

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
                @foreach($servicios as $i => $srv)
                    <div data-aos="fade-up" data-aos-delay="{{ $i * 100 }}" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 2rem; display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: default;"
                         onmouseenter="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 40px rgba(0,0,0,0.3)'"
                         onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                        <div style="width: 52px; height: 52px; border-radius: 12px; background: {{ $srv['color'] }}20; color: {{ $srv['color'] }}; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 1.25rem;">
                            <i class="{{ $srv['icon'] }}"></i>
                        </div>
                        <h3 style="color: white; font-size: 1.15rem; font-weight: 700; margin-bottom: 0.75rem;">{{ $srv['title'] }}</h3>
                        <p style="color: var(--slate-400); font-size: 0.9rem; line-height: 1.6; flex: 1; margin-bottom: 1.5rem;">{{ $srv['desc'] }}</p>
                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($srv['wa_msg']) }}" target="_blank" rel="noopener"
                           style="display: inline-flex; align-items: center; gap: 0.5rem; color: #25D366; font-weight: 600; font-size: 0.88rem; text-decoration: none; transition: opacity 0.2s;"
                           onmouseenter="this.style.opacity='0.8'" onmouseleave="this.style.opacity='1'">
                            <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem;"></i> Cotizar por WhatsApp
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
