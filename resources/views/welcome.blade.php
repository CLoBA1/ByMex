@extends('layouts.public')

@section('title', 'Inicio')

@section('content')
    <!-- Horizontal Scroll Destinos Destacados -->
    <section class="section-pad bg-light" id="tours" style="padding-top: 8rem;">
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

@endsection
