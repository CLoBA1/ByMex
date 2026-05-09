@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
    <!-- Hero Swiper (Pantalla Completa) -->
    <section class="hero-swiper-container">
        <div class="swiper heroSwiper" style="width: 100%; height: 100%;">
            <div class="swiper-wrapper">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <picture>
                                @if($banner->image_mobile)
                                    <source media="(max-width: 768px)" srcset="{{ Storage::url($banner->image_mobile) }}">
                                @endif
                                <img src="{{ Storage::url($banner->image_desktop) }}" class="hero-slide-bg" alt="{{ $banner->title ?? 'Banner Promocional' }}">
                            </picture>
                            <div class="hero-overlay"></div>
                            <div class="hero-content">
                                @if($banner->title)
                                    <h1 data-aos="fade-up" data-aos-delay="200" style="color:white; font-size:clamp(2.5rem, 6vw, 4.5rem); margin-bottom:1rem; font-weight:800; text-transform:uppercase; letter-spacing:2px; line-height:1.1;">
                                        {!! nl2br(e($banner->title)) !!}
                                    </h1>
                                @endif
                                @if($banner->subtitle)
                                    <p data-aos="fade-up" data-aos-delay="300" style="color:#e2e8f0; font-size:1.2rem; max-width:600px; margin-bottom:2rem;">{{ $banner->subtitle }}</p>
                                @endif
                                @if($banner->link && $banner->button_text)
                                    <a href="{{ $banner->link }}" class="btn btn-primary" style="font-size:1.1rem; padding:1rem 2rem;" data-aos="zoom-in" data-aos-delay="400">
                                        {{ $banner->button_text }} <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback if no banners -->
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
                @endif
            </div>
            @if(isset($banners) && $banners->count() > 1)
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            @endif
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
                                <img src="{{ $tour->image ? Storage::url($tour->image) : $imgs[$i % 3] }}" alt="{{ $tour->destination }}" class="card-bg">
                                <div class="card-gradient"></div>
                                <div class="card-content">
                                    <div class="card-tags">
                                        @if($i === 1)<span class="tag hot"><i class="fa-solid fa-fire"></i> Más vendido</span>@endif
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--slate-500); margin-bottom: 0.5rem; font-weight: 500;">
                                        <i class="fa-solid fa-map-pin" style="color: var(--primary);"></i> Salidas desde: Acapulco, Chilpancingo e Iguala
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
