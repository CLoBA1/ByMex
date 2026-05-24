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

    <!-- Catálogo de Servicios -->
    <section class="section-pad" id="servicios" style="background: var(--bg-body);">
        <div class="container" style="max-width: 1200px;">
            @if($categories->isEmpty())
                <div style="text-align: center; padding: 4rem 0;">
                    <i class="fa-solid fa-box-open" style="font-size: 3rem; color: var(--slate-300); margin-bottom: 1rem;"></i>
                    <p style="color: var(--slate-500); font-size: 1.1rem;">Por el momento no hay servicios disponibles. Vuelve pronto.</p>
                </div>
            @endif

            @foreach($categories as $category)
                <div style="margin-bottom: 4rem;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid var(--border); padding-bottom: 1rem;">
                        @if($category->icon)
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                                <i class="{{ $category->icon }}"></i>
                            </div>
                        @endif
                        <div>
                            <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--navy); margin-bottom: 0.25rem;">{{ $category->name }}</h2>
                            @if($category->description)
                                <p style="color: var(--slate-500); margin: 0;">{{ $category->description }}</p>
                            @endif
                        </div>
                    </div>

                    @if($category->options->isEmpty())
                        <p style="color: var(--slate-400); font-style: italic;">Próximamente nuevas opciones en esta categoría.</p>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
                            @foreach($category->options as $i => $option)
                                <div data-aos="fade-up" data-aos-delay="{{ ($i % 4) * 100 }}" style="background: white; border: 1px solid var(--border); border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.3s ease, box-shadow 0.3s ease;"
                                     onmouseenter="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.08)'"
                                     onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
                                    
                                    @if($option->image)
                                        <div style="height: 180px; width: 100%; overflow: hidden;">
                                            <img src="{{ Storage::url($option->image) }}" alt="{{ $option->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    @endif
                                    
                                    <div style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1;">
                                        <h3 style="color: var(--navy); font-size: 1.2rem; font-weight: 700; margin-bottom: 0.75rem;">{{ $option->name }}</h3>
                                        <p style="color: var(--slate-500); font-size: 0.95rem; line-height: 1.6; flex: 1; margin-bottom: 1.5rem;">{{ $option->description }}</p>
                                        
                                        <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($option->whatsapp_message) }}" target="_blank" rel="noopener"
                                           class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: var(--navy); border: none;">
                                            <i class="fa-brands fa-whatsapp" style="font-size: 1.1rem; color: #25D366;"></i> Quiero información
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
@endsection
