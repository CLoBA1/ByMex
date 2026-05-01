@extends('layouts.public')

@section('title', 'Catálogo de Destinos')

@section('content')
    <div style="background: var(--navy); padding: 6rem 0 3rem; text-align:center; color: white;">
        <div class="container">
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Nuestros Destinos</h1>
            <p style="color: var(--slate-400); max-width: 600px; margin: 0 auto;">Encuentra el viaje perfecto para tus próximas vacaciones. Todos nuestros tours incluyen transporte de lujo y hospedaje 4 estrellas.</p>
        </div>
    </div>

    <section class="section-pad bg-light">
        <div class="container">
            <div class="catalog-container">
                <!-- Sidebar Filtros -->
                <aside class="catalog-sidebar" data-aos="fade-right">
                    <h4><i class="fa-solid fa-filter" style="color:var(--primary); margin-right:.5rem;"></i> Filtros</h4>
                    
                    <div class="catalog-filter-group">
                        <label>Mes de Salida</label>
                        <select>
                            <option value="">Cualquier fecha</option>
                            <option value="2026-05">Mayo 2026</option>
                            <option value="2026-06">Junio 2026</option>
                            <option value="2026-07">Julio 2026</option>
                        </select>
                    </div>

                    <div class="catalog-filter-group">
                        <label>Tipo de Viaje</label>
                        <select>
                            <option value="">Todos</option>
                            <option value="playa">Playa</option>
                            <option value="santuarios">Santuarios</option>
                            <option value="pueblos">Pueblos Mágicos</option>
                        </select>
                    </div>

                    <div class="catalog-filter-group">
                        <label>Precio Máximo</label>
                        <input type="range" min="1000" max="20000" step="500" style="width:100%; accent-color:var(--primary);">
                        <div style="display:flex; justify-content:space-between; font-size:.8rem; color:var(--slate-500); margin-top:.5rem;">
                            <span>$1,000</span>
                            <span>$20,000+</span>
                        </div>
                    </div>

                    <button class="btn btn-primary btn-block" style="margin-top: 1rem;">Aplicar Filtros</button>
                </aside>

                <!-- Grid de Tours -->
                <div class="catalog-grid">
                    @forelse($tours ?? [] as $i => $tour)
                        @php
                            $imgs=['https://images.unsplash.com/photo-1579606032851-069ee84eb694?auto=format&fit=crop&q=80&w=600','https://images.unsplash.com/photo-1582650517303-b42616d56fba?auto=format&fit=crop&q=80&w=600','https://images.unsplash.com/photo-1626211825121-a3fcfb64f3d2?auto=format&fit=crop&q=80&w=600'];
                            $taken = $tour->seats()->count();
                            $pct = $tour->total_seats > 0 ? round(($taken / $tour->total_seats) * 100) : 0;
                            $avail = $tour->total_seats - $taken;
                        @endphp
                        <a href="{{ route('tours.show', $tour->id) }}" class="tour-card-immersive" data-aos="fade-up" data-aos-delay="{{ ($i % 3) * 100 }}">
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
                    @empty
                        <div style="grid-column: 1/-1; text-align:center; padding: 4rem; background:white; border-radius:var(--radius-xl); border:1px solid var(--slate-100);">
                            <i class="fa-solid fa-suitcase-rolling" style="font-size: 3rem; color: var(--slate-300); margin-bottom:1rem;"></i>
                            <h3 style="color:var(--navy);">No hay destinos disponibles por el momento</h3>
                            <p style="color:var(--slate-500);">Estamos preparando nuevos viajes increíbles para ti.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
