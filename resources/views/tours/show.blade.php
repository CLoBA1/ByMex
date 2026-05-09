@extends('layouts.public')

@section('title', 'Reservar: ' . $tour->title)

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/tour.css') }}">
    <style>
        /* Mejoras de legibilidad general */
        .info-card p, .info-card ul li {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--navy);
        }
        
        /* Sistema "Ver más" / "Ver menos" */
        .expandable-text {
            position: relative;
            overflow: hidden;
            max-height: 6.4rem; /* Aprox 4 líneas */
            transition: max-height 0.3s ease-out;
        }
        .expandable-text.expanded {
            max-height: 5000px; /* Suficiente para texto largo */
            transition: max-height 0.5s ease-in;
        }
        .expandable-text.has-overflow::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 45px;
            background: linear-gradient(to bottom, transparent, #ffffff);
            pointer-events: none;
            transition: opacity 0.2s;
        }
        .expandable-text.expanded::after {
            opacity: 0;
        }
        .btn-ver-mas {
            display: none;
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 700;
            cursor: pointer;
            padding: 0;
            margin-top: 0.5rem;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .btn-ver-mas:hover {
            color: var(--navy);
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    {{-- ============================================================
         HERO BANNER (Full-width image)
         ============================================================ --}}
    <header class="tour-hero" style="background-image: url('{{ $tour->image ? Storage::url($tour->image) : 'https://images.unsplash.com/photo-1582650517303-b42616d56fba?auto=format&fit=crop&q=80&w=1920' }}');">
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
                        <span><i class="fa-solid fa-map-pin"></i> Salidas desde: Acapulco, Chilpancingo e Iguala</span>
                    </div>
                </div>

                {{-- Card: Description --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="100">
                    <h3><i class="fa-solid fa-clipboard-list"></i> Descripción del Viaje</h3>
                    <div class="expandable-text js-expandable">
                        <p>{!! nl2br(e($tour->description ?? 'Disfruta de una experiencia inolvidable. Nuestro viaje está diseñado para que te relajes y disfrutes al máximo, nosotros nos encargamos de la logística, el transporte y la seguridad.')) !!}</p>
                    </div>
                    <button class="btn-ver-mas js-btn-expand" type="button">Ver más <i class="fa-solid fa-chevron-down"></i></button>
                </div>

                {{-- Card: Itinerary --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="150">
                    <h3><i class="fa-solid fa-map-location-dot"></i> Itinerario</h3>
                    <p style="color: var(--slate-500); font-style: italic;">Itinerario disponible próximamente.</p>
                </div>

                {{-- Card: What's Included --}}
                <div class="info-card" data-aos="fade-up" data-aos-delay="200" style="background: var(--slate-50); box-shadow: none; border: 1px solid var(--slate-200);">
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
                <div class="info-card" data-aos="fade-up" data-aos-delay="300" style="background: var(--slate-50); box-shadow: none; border: 1px solid var(--slate-200);">
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

                    {{-- Initial Booking Button --}}
                    <div id="bookingStartArea" style="padding: 2rem 1.5rem; text-align: center;">
                        <button class="bk-cta-btn" id="btnStartFlow" style="font-size: 1.1rem;">
                            Reservar Ahora <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <p style="color: var(--slate-500); font-size: 0.85rem; margin-top: 1rem;">Seleccionarás tus asientos en el siguiente paso.</p>
                    </div>

                    {{-- Bus Map and Summary (Hidden initially) --}}
                    <div id="bookingFlowContainer" style="display: none;">
                        {{-- Bus Map (Dark Glassmorphism) --}}
                        <div class="bk-bus-section">
                        <div class="bus-frame">
                            <div class="bus-front-label">
                                <i class="fa-solid fa-tv"></i> Frente del Autobús — Elige tus asientos
                            </div>

                            @include('partials._bus_map', ['mode' => 'public', 'tour' => $tour])

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
                    </div> <!-- End bookingFlowContainer -->

                </div>
            </aside>

        </div>
    </section>

    {{-- Hidden inputs for JS --}}
    <input type="hidden" id="tourId" value="{{ $tour->id }}">
    <input type="hidden" id="tourPrice" value="{{ $tour->price }}">
    <input type="hidden" id="tourDuration" value="{{ $tour->duration_days ?? 1 }}">

    {{-- ============================================================
         POLICIES MODAL
         ============================================================ --}}
    @php
        $settings = \App\Models\PaymentSetting::first();
        $reservationPolicies = $settings->reservation_policies ?? 'Las políticas de reservación estarán disponibles próximamente.';
        $cancellationPolicies = $settings->cancellation_policies ?? 'Las políticas de cancelación estarán disponibles próximamente.';
    @endphp
    <div class="modal-overlay" id="policiesModal" style="z-index: 10000;">
        <div class="modal-content" style="max-width: 600px;">
            <button class="close-modal" id="closePoliciesModal">&times;</button>
            <h2 class="modal-title" style="margin-bottom: 1.5rem;"><i class="fa-solid fa-file-contract" style="color:var(--primary);"></i> Políticas del Viaje</h2>
            
            <div style="max-height: 50vh; overflow-y: auto; padding-right: 1rem; margin-bottom: 1.5rem; color: var(--slate-600); font-size: 0.95rem; line-height: 1.6;">
                <h4 style="color: var(--navy); margin-bottom: 0.5rem; font-weight: 700;">Políticas de Reservación</h4>
                <p style="margin-bottom: 1.5rem; white-space: pre-line;">{{ $reservationPolicies }}</p>
                
                <h4 style="color: var(--navy); margin-bottom: 0.5rem; font-weight: 700;">Políticas de Cancelación</h4>
                <p style="margin-bottom: 1rem; white-space: pre-line;">{{ $cancellationPolicies }}</p>
            </div>

            <div style="background: var(--slate-50); padding: 1rem; border-radius: 8px; border: 1px solid var(--slate-200); margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="chkAcceptPolicies" style="width: 20px; height: 20px; margin-top: 2px; accent-color: var(--primary);">
                    <span style="font-weight: 600; color: var(--navy);">He leído y acepto las políticas de reservación y cancelación para este viaje.</span>
                </label>
            </div>

            <button class="bk-cta-btn" id="btnAcceptPolicies" disabled style="opacity: 0.5;">
                Aceptar y Continuar a Asientos <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

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
        window.BOARDING_POINTS = @json($boardingPoints);
        
        // Lógica de "Ver más / Ver menos"
        document.addEventListener('DOMContentLoaded', function() {
            const expandableBlocks = document.querySelectorAll('.js-expandable');
            
            expandableBlocks.forEach(block => {
                const btn = block.nextElementSibling;
                // Verificar si el contenido desborda el max-height actual
                if (block.scrollHeight > block.clientHeight) {
                    block.classList.add('has-overflow');
                    btn.style.display = 'inline-block';
                    
                    btn.addEventListener('click', function() {
                        block.classList.toggle('expanded');
                        if (block.classList.contains('expanded')) {
                            this.innerHTML = 'Ver menos <i class="fa-solid fa-chevron-up"></i>';
                        } else {
                            this.innerHTML = 'Ver más <i class="fa-solid fa-chevron-down"></i>';
                        }
                    });
                }
            });
        });
        // Lógica del Modal de Políticas
        const btnStartFlow = document.getElementById('btnStartFlow');
        const policiesModal = document.getElementById('policiesModal');
        const closePoliciesModal = document.getElementById('closePoliciesModal');
        const chkAcceptPolicies = document.getElementById('chkAcceptPolicies');
        const btnAcceptPolicies = document.getElementById('btnAcceptPolicies');
        const bookingStartArea = document.getElementById('bookingStartArea');
        const bookingFlowContainer = document.getElementById('bookingFlowContainer');

        if (btnStartFlow && policiesModal) {
            btnStartFlow.addEventListener('click', () => {
                policiesModal.classList.add('active');
            });

            closePoliciesModal.addEventListener('click', () => {
                policiesModal.classList.remove('active');
            });

            chkAcceptPolicies.addEventListener('change', (e) => {
                if (e.target.checked) {
                    btnAcceptPolicies.disabled = false;
                    btnAcceptPolicies.style.opacity = '1';
                } else {
                    btnAcceptPolicies.disabled = true;
                    btnAcceptPolicies.style.opacity = '0.5';
                }
            });

            btnAcceptPolicies.addEventListener('click', () => {
                policiesModal.classList.remove('active');
                bookingStartArea.style.display = 'none';
                bookingFlowContainer.style.display = 'block';
                // Trigger resize or custom event if the bus map needs it to render properly when unhidden
                window.dispatchEvent(new Event('resize'));
            });
        }
    </script>
    <script src="{{ asset('js/tour.js') }}?v={{ filemtime(public_path('js/tour.js')) }}"></script>
@endsection
