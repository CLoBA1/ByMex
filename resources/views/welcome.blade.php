@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
    <!-- Hero Swiper (Pantalla Completa) -->
    <section class="hero-swiper-container">
        <div class="swiper heroSwiper" style="width: 100%; height: 100%;">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <img src="https://images.unsplash.com/photo-1512813195386-6cf811ad3542?auto=format&fit=crop&q=80&w=1920" class="hero-slide-bg" alt="México">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="hero-badge" data-aos="fade-down" data-aos-delay="100"><i class="fa-solid fa-star"></i> EXPERIENCIAS PREMIUM</div>
                        <h1 data-aos="fade-up" data-aos-delay="200" style="color:white; font-size:clamp(2.5rem, 6vw, 4.5rem); margin-bottom:1rem; font-weight:800; text-transform:uppercase; letter-spacing:2px; line-height:1.1;">
                            Descubre México<br><span style="color:var(--gold);">A Otro Nivel</span>
                        </h1>
                        <p data-aos="fade-up" data-aos-delay="300" style="color:#e2e8f0; font-size:1.2rem; max-width:600px; margin-bottom:2rem;">Autobuses de lujo, hospedaje 4 estrellas y atención personalizada.</p>
                        <a href="{{ route('tours.index') }}" class="btn btn-primary" style="font-size:1.1rem; padding:1rem 2rem;" data-aos="zoom-in" data-aos-delay="400">Ver Catálogo <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <img src="https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?auto=format&fit=crop&q=80&w=1920" class="hero-slide-bg" alt="Naturaleza">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="hero-badge"><i class="fa-solid fa-leaf"></i> NATURALEZA Y CULTURA</div>
                        <h1 style="color:white; font-size:clamp(2.5rem, 6vw, 4.5rem); margin-bottom:1rem; font-weight:800; text-transform:uppercase; letter-spacing:2px; line-height:1.1;">
                            Conecta con tus<br><span style="color:var(--teal);">Raíces</span>
                        </h1>
                        <p style="color:#e2e8f0; font-size:1.2rem; max-width:600px; margin-bottom:2rem;">Visita santuarios, playas y pueblos mágicos con total comodidad.</p>
                        <a href="{{ route('tours.index') }}" class="btn btn-primary" style="font-size:1.1rem; padding:1rem 2rem;">Reservar Ahora <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <img src="https://images.unsplash.com/photo-1582650517303-b42616d56fba?auto=format&fit=crop&q=80&w=1920" class="hero-slide-bg" alt="Santuarios">
                    <div class="hero-overlay"></div>
                    <div class="hero-content">
                        <div class="hero-badge"><i class="fa-solid fa-bus"></i> VIAJA SEGURO</div>
                        <h1 style="color:white; font-size:clamp(2.5rem, 6vw, 4.5rem); margin-bottom:1rem; font-weight:800; text-transform:uppercase; letter-spacing:2px; line-height:1.1;">
                            Tu única tarea es<br><span style="color:var(--primary);">Disfrutar</span>
                        </h1>
                        <p style="color:#e2e8f0; font-size:1.2rem; max-width:600px; margin-bottom:2rem;">Nosotros nos encargamos de todo lo demás.</p>
                        <a href="{{ route('about') }}" class="btn btn-outline" style="border-width:2px; color:white; border-color:white; font-size:1.1rem; padding:1rem 2rem;">Conoce Nuestra Historia <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Floating Search Widget -->
    <div class="search-widget-floating" data-aos="fade-up" data-aos-delay="500">
        <div class="form-group">
            <label>Destino</label>
            <select id="searchDest">
                <option value="">¿A dónde vamos?</option>
                @foreach($tours ?? [] as $t)
                    <option value="{{ $t->id }}">{{ $t->destination }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Mes de Viaje</label>
            <input type="month" value="{{ date('Y-m') }}">
        </div>
        <div class="form-group">
            <label>Pasajeros</label>
            <input type="number" min="1" max="10" value="2">
        </div>
        <button class="btn btn-primary btn-search" id="btnSearch" onclick="window.location.href='{{ route('tours.index') }}'"><i class="fa-solid fa-magnifying-glass" style="margin-right: .5rem;"></i> Buscar</button>
    </div>

    <!-- Trust Bar -->
    <section class="trust-bar" style="position:relative; z-index:10; margin-top: 2rem;">
        <div class="container">
            <div class="trust-grid" style="background:var(--white); padding:2rem; border-radius:var(--radius-xl); border:1px solid var(--slate-100);">
                <div class="trust-item" data-aos="zoom-in" data-aos-delay="0">
                    <div class="trust-icon red"><i class="fa-solid fa-bus"></i></div><span>Autobuses de lujo</span>
                </div>
                <div class="trust-item" data-aos="zoom-in" data-aos-delay="100">
                    <div class="trust-icon gold"><i class="fa-solid fa-hotel"></i></div><span>Hoteles 4 estrellas</span>
                </div>
                <div class="trust-item" data-aos="zoom-in" data-aos-delay="200">
                    <div class="trust-icon teal"><i class="fa-solid fa-shield-heart"></i></div><span>Seguro de viajero</span>
                </div>
                <div class="trust-item" data-aos="zoom-in" data-aos-delay="300">
                    <div class="trust-icon navy"><i class="fa-solid fa-headset"></i></div><span>Atención personalizada</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Horizontal Scroll Destinos Destacados -->
    <section class="section-pad bg-light" id="tours">
        <div class="container" style="max-width: 1400px; padding: 0;">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div class="section-label" data-aos="fade-up"><i class="fa-solid fa-circle"></i> PRÓXIMAS SALIDAS</div>
                <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Destinos Destacados</h2>
                <a href="{{ route('tours.index') }}" class="btn btn-outline" style="margin-top: 1rem;" data-aos="fade-up" data-aos-delay="200">Ver Catálogo Completo <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="horizontal-scroll-wrapper" data-aos="fade-up" data-aos-delay="300">
                <div class="horizontal-scroll-container">
                    @forelse($tours->take(4) ?? [] as $i => $tour)
                        @php
                            $imgs=['https://images.unsplash.com/photo-1579606032851-069ee84eb694?auto=format&fit=crop&q=80&w=600','https://images.unsplash.com/photo-1582650517303-b42616d56fba?auto=format&fit=crop&q=80&w=600','https://images.unsplash.com/photo-1626211825121-a3fcfb64f3d2?auto=format&fit=crop&q=80&w=600'];
                        @endphp
                        <div class="horizontal-scroll-item">
                            <a href="{{ route('tours.show', $tour->id) }}" class="tour-card-immersive">
                                <img src="{{ $tour->image ? asset($tour->image) : $imgs[$i % 3] }}" alt="{{ $tour->destination }}" class="card-bg">
                                <div class="card-gradient"></div>
                                <div class="card-content">
                                    <div class="card-tags">
                                        @if($i === 1)<span class="tag hot"><i class="fa-solid fa-fire"></i> Más vendido</span>@endif
                                        <span class="tag"><i class="fa-solid fa-bus"></i> Transporte Lujo</span>
                                    </div>
                                    <h3 class="card-title">{{ $tour->title }}</h3>
                                    <div class="card-date"><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($tour->departure_date)->translatedFormat('d \d\e F') }}</div>
                                    <div class="card-footer">
                                        <div class="price-box">
                                            <span>Desde</span>
                                            <h4>${{ number_format($tour->price, 0) }}</h4>
                                        </div>
                                        <div class="btn-book">Reservar <i class="fa-solid fa-arrow-right"></i></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <p style="grid-column:1/-1;color:var(--slate-400); text-align:center;">Pronto agregaremos nuevos destinos.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Masonry Gallery -->
    <section class="section-pad">
        <div class="container text-center">
            <div class="section-label" data-aos="fade-up"><i class="fa-solid fa-circle"></i> EXPERIENCIAS RECOMENDADAS</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Momentos Inolvidables By Mex</h2>
            <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:3rem;">Nuestros viajeros son nuestra mejor garantía.</p>

            <div class="masonry-gallery" data-aos="fade-up" data-aos-delay="300">
                <div class="masonry-item tall">
                    <img src="https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?auto=format&fit=crop&q=80&w=800" alt="Viajeros">
                </div>
                <div class="masonry-item square">
                    <img src="https://images.unsplash.com/photo-1527668752968-14ce70a27dd3?auto=format&fit=crop&q=80&w=600" alt="Paisaje">
                </div>
                <div class="masonry-item square">
                    <img src="https://images.unsplash.com/photo-1501504905252-473c47e087f8?auto=format&fit=crop&q=80&w=600" alt="Playa">
                </div>
                <div class="masonry-item wide">
                    <img src="https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&q=80&w=1200" alt="Piramide">
                </div>
            </div>
        </div>
    </section>

@endsection

@section('extra-js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if(typeof Swiper !== 'undefined') {
            const heroSwiper = new Swiper('.heroSwiper', {
                loop: true,
                effect: 'fade',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    });
</script>
@endsection
