@extends('layouts.public')

@section('title', 'Reservar: ' . $tour->title)

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/tour.css') }}">
@endsection

@section('content')
    {{-- ============================================================
         HERO BANNER (Full-width image)
         ============================================================ --}}
    <header class="tour-hero" style="background-image: url('{{ $tour->image ? asset($tour->image) : 'https://images.unsplash.com/photo-1582650517303-b42616d56fba?auto=format&fit=crop&q=80&w=1920' }}');">
        <div class="tour-hero-content">
            <h1>{{ $tour->title }}</h1>
        </div>
    </header>

    {{-- ============================================================
         MAIN CONTENT: 2-Column Layout
         ============================================================ --}}
    <section class="tour-page-body">
        <div class="tour-split">

            {{-- ============================
                 LEFT COLUMN: Tour Information
                 ============================ --}}
            <div class="tour-info-col">

                {{-- Card: Tour Header --}}
                <div class="info-card info-card--header" data-aos="fade-up">
                    <h2>{{ $tour->title }}</h2>
                    <div class="tour-meta-row">
                        <span><i class="fa-regular fa-calendar"></i> Salida: {{ \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d \d\e F Y - H:i') }} hrs</span>
                        <span><i class="fa-solid fa-users"></i> {{ $tour->total_seats }} Lugares en total</span>
                        <span><i class="fa-solid fa-bus"></i> Transporte Lujo</span>
                    </div>
                </div>

                {{-- Card: Description --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                    <h3><i class="fa-solid fa-clipboard-list"></i> Descripción del Viaje</h3>
                    <p>{{ $tour->description ?? 'Disfruta de una experiencia inolvidable. Nuestro viaje está diseñado para que te relajes y disfrutes al máximo, nosotros nos encargamos de la logística, el transporte y la seguridad.' }}</p>
                </div>

                {{-- Card: What's Included --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="200">
                    <h3><i class="fa-solid fa-check-circle"></i> Qué Incluye</h3>
                    <ul class="feature-grid">
                        <li><i class="fa-solid fa-bus"></i> Transporte viaje redondo</li>
                        <li><i class="fa-solid fa-shield-heart"></i> Seguro de viajero a bordo</li>
                        <li><i class="fa-solid fa-user-tie"></i> Coordinador de grupo</li>
                        <li><i class="fa-solid fa-camera"></i> Visitas guiadas</li>
                        <li><i class="fa-solid fa-bottle-water"></i> Hidratación en el autobús</li>
                    </ul>
                </div>

                {{-- Card: Not Included --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="300">
                    <h3><i class="fa-solid fa-circle-xmark red"></i> No Incluye</h3>
                    <ul class="feature-grid not-included">
                        <li><i class="fa-solid fa-utensils"></i> Alimentos no mencionados</li>
                        <li><i class="fa-solid fa-ticket"></i> Propinas</li>
                        <li><i class="fa-solid fa-bag-shopping"></i> Gastos personales</li>
                    </ul>
                </div>
            </div>

            {{-- ============================
                 RIGHT COLUMN: Booking Widget
                 ============================ --}}
            <aside class="tour-booking-col" data-aos="fade-left">
                <div class="bk-widget">

                    {{-- Price Header --}}
                    <div class="bk-price-header">
                        <div class="label">Precio por persona</div>
                        <div class="price">${{ number_format($tour->price, 0) }} MXN</div>
                    </div>

                    {{-- Bus Map (Dark Glassmorphism) --}}
                    <div class="bk-bus-section">
                        <div class="bus-frame">
                            <div class="bus-front-label">
                                <i class="fa-solid fa-tv"></i> Frente del Autobús — Elige tus asientos
                            </div>

                            <div class="bus-seat-grid">
                                @php
                                    $totalSeats = $tour->total_seats ?? 36;
                                    $rows = ceil($totalSeats / 4);
                                    $seatNum = 1;
                                @endphp

                                @for ($r = 0; $r < $rows; $r++)
                                    {{-- Left pair (seats in columns 1-2) --}}
                                    @for ($c = 0; $c < 2; $c++)
                                        @if ($seatNum <= $totalSeats)
                                            @php $sn = str_pad($seatNum, 2, "0", STR_PAD_LEFT); @endphp
                                            <div class="seat" data-seat="{{ $seatNum }}">{{ $sn }}</div>
                                            @php $seatNum++; @endphp
                                        @else
                                            <div></div>
                                        @endif
                                    @endfor

                                    {{-- Right pair (seats in columns 3-4) --}}
                                    @for ($c = 0; $c < 2; $c++)
                                        @if ($seatNum <= $totalSeats)
                                            @php $sn = str_pad($seatNum, 2, "0", STR_PAD_LEFT); @endphp
                                            <div class="seat" data-seat="{{ $seatNum }}">{{ $sn }}</div>
                                            @php $seatNum++; @endphp
                                        @else
                                            <div></div>
                                        @endif
                                    @endfor

                                    {{-- Aisle spacer after every 2 rows --}}
                                    @if ($r == floor($rows / 3) - 1 || $r == floor(2 * $rows / 3) - 1)
                                        <div class="seat-row-spacer"></div>
                                    @endif
                                @endfor
                            </div>

                            <div class="bus-legend">
                                <div class="legend-item"><div class="legend-box available"></div> Libre</div>
                                <div class="legend-item"><div class="legend-box selected"></div> Tu Selección</div>
                                <div class="legend-item"><div class="legend-box occupied"></div> Ocupado</div>
                            </div>
                        </div>
                    </div>

                    {{-- Booking Summary (Light) --}}
                    <div class="bk-summary">
                        <div class="bk-summary-title"><i class="fa-solid fa-ticket"></i> Detalle de tu Reserva</div>

                        <div class="bk-seats-badges" id="selectedSeatsList">
                            <span class="empty-msg">Selecciona tus asientos arriba</span>
                        </div>

                        <div class="bk-subtotal-line">
                            <span>Subtotal:</span>
                            <span class="value" id="subtotal">$0</span>
                        </div>
                        <div class="bk-total-line">
                            <span class="label">Total a Pagar</span>
                            <span class="value" id="total">$0 MXN</span>
                        </div>

                        <button class="bk-cta-btn" id="btnContinuar" disabled>
                            Continuar con el Pago <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <div class="bk-secure-label">
                            <i class="fa-solid fa-lock"></i> Proceso de reserva 100% seguro
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </section>

    {{-- Hidden inputs for JS --}}
    <input type="hidden" id="tourId" value="{{ $tour->id }}">
    <input type="hidden" id="tourPrice" value="{{ $tour->price }}">

    {{-- ============================================================
         CHECKOUT MODAL
         ============================================================ --}}
    <div class="modal-overlay" id="checkoutModal">
        <div class="modal-content">
            <button class="close-modal" id="closeModal">&times;</button>
            <h2 class="modal-title">Datos del Pasajero Principal</h2>
            <p class="modal-subtitle">Total: <strong id="totalModal" style="color:var(--primary);font-size:1.15rem;font-weight:900;">$0 MXN</strong></p>
            
            <form action="{{ route('reservations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                <input type="hidden" name="seats" id="selectedSeatsInput" value="">
                
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" class="form-control" name="name" required placeholder="Ej. Juan Pérez">
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label>Teléfono Celular</label>
                        <input type="tel" class="form-control" name="phone" required placeholder="10 dígitos">
                    </div>
                    <div class="form-group">
                        <label>WhatsApp (Opcional)</label>
                        <input type="tel" class="form-control" name="whatsapp" placeholder="Si es diferente">
                    </div>
                </div>
                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" class="form-control" name="email" required placeholder="tu@correo.com">
                </div>

                <!-- Contenedor dinámico de pasajeros -->
                <div id="passengersContainer" style="margin-top: 1.5rem;"></div>

                <div class="form-group" style="margin-top:2rem;">
                    <button type="submit" class="bk-cta-btn"><i class="fa-solid fa-lock"></i> Confirmar Reserva</button>
                    <p style="text-align:center;font-size:.75rem;color:var(--slate-400);margin-top:1rem;">
                        Tus asientos quedarán apartados por {{ $tour->expiration_hours ?? 24 }} horas en espera de tu anticipo.
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        window.API_URL_SEATS = "{{ url('api/seats') }}/{{ $tour->id }}";
    </script>
    <script src="{{ asset('js/tour.js') }}?v={{ filemtime(public_path('js/tour.js')) }}"></script>
@endsection
