@extends('layouts.public')

@section('title', 'Nuestra Historia')

@section('content')
    <!-- Hero About -->
    <header style="background: linear-gradient(rgba(13,27,42,0.8), rgba(13,27,42,0.9)), url('https://images.unsplash.com/photo-1518105779142-d975f22f1b0a?auto=format&fit=crop&q=80&w=1920') center/cover; padding: 10rem 0 6rem; text-align: center;">
        <div class="container">
            <h1 data-aos="fade-up" style="color:white; font-size:clamp(2.5rem, 5vw, 4rem); font-weight:800; margin-bottom:1rem;">Nuestra Historia</h1>
            <p data-aos="fade-up" data-aos-delay="100" style="color:var(--slate-300); font-size:1.2rem; max-width:700px; margin:0 auto;">Más de 10 años recorriendo los caminos de México, creando memorias y conectando familias.</p>
        </div>
    </header>

    <!-- Content Split -->
    <section class="section-pad">
        <div class="container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 4rem; align-items: center;">
            <div data-aos="fade-right">
                <div class="section-label"><i class="fa-solid fa-circle"></i> EL ORIGEN</div>
                <h2 class="section-title">De una idea familiar a una gran agencia.</h2>
                <p style="color:var(--slate-500); line-height:1.8; margin-bottom:1.5rem; font-size:1.05rem;">
                    <strong>Viajes By Mex</strong> nació con un propósito muy claro: hacer que viajar por México fuera seguro, cómodo y accesible para todas las familias.
                </p>
                <p style="color:var(--slate-500); line-height:1.8; margin-bottom:1.5rem; font-size:1.05rem;">
                    Comenzamos con excursiones locales los fines de semana, y gracias a la confianza de miles de viajeros, hoy operamos rutas a los santuarios más importantes, las playas más hermosas y los rincones culturales más escondidos del país.
                </p>
                <div style="background: var(--slate-50); padding: 1.5rem; border-left: 4px solid var(--primary); border-radius: 0 var(--radius-lg) var(--radius-lg) 0;">
                    <p style="font-style: italic; color:var(--navy); font-weight:600; margin:0;">"No vendemos asientos de autobús, creamos la oportunidad de que vivas experiencias que recordarás toda la vida."</p>
                </div>
            </div>
            <div data-aos="fade-left" style="position:relative;">
                <img src="https://images.unsplash.com/photo-1527668752968-14ce70a27dd3?auto=format&fit=crop&q=80&w=800" alt="Viajes" style="border-radius: var(--radius-2xl); box-shadow: var(--shadow-2xl); width: 100%;">
                <div style="position:absolute; bottom:-20px; left:-20px; background:white; padding:1.5rem; border-radius:var(--radius-xl); box-shadow:var(--shadow-lg);">
                    <div style="font-size: 2.5rem; color:var(--primary); font-weight:800; line-height:1;">+5K</div>
                    <div style="font-size: .85rem; color:var(--slate-500); font-weight:600; text-transform:uppercase;">Viajeros Felices</div>
                </div>
            </div>
        </div>
    </section>

    <!-- La Flota -->
    <section class="section-pad bg-light">
        <div class="container text-center">
            <div class="section-label" data-aos="fade-up"><i class="fa-solid fa-circle"></i> SEGURIDAD Y CONFORT</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Nuestra Flota</h2>
            <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:4rem;">Viajamos exclusivamente en unidades de modelo reciente con los más altos estándares de seguridad.</p>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:2rem;">
                <div style="background:white; padding:2rem; border-radius:var(--radius-xl); box-shadow:var(--shadow-md);" data-aos="zoom-in" data-aos-delay="0">
                    <i class="fa-solid fa-bus" style="font-size: 2.5rem; color:var(--red); margin-bottom:1rem;"></i>
                    <h4 style="font-size:1.2rem; margin-bottom:.5rem; color:var(--navy);">Autobuses Irizar I8</h4>
                    <p style="color:var(--slate-500); font-size:.9rem;">Asientos reclinables, clima, pantallas HD y sanitarios. Ideal para viajes largos a santuarios.</p>
                </div>
                <div style="background:white; padding:2rem; border-radius:var(--radius-xl); box-shadow:var(--shadow-md);" data-aos="zoom-in" data-aos-delay="100">
                    <i class="fa-solid fa-van-shuttle" style="font-size: 2.5rem; color:var(--gold); margin-bottom:1rem;"></i>
                    <h4 style="font-size:1.2rem; margin-bottom:.5rem; color:var(--navy);">Sprinter Mercedes Benz</h4>
                    <p style="color:var(--slate-500); font-size:.9rem;">Para grupos pequeños y rutas a pueblos mágicos. Ágiles, cómodas y con aire acondicionado.</p>
                </div>
                <div style="background:white; padding:2rem; border-radius:var(--radius-xl); box-shadow:var(--shadow-md);" data-aos="zoom-in" data-aos-delay="200">
                    <i class="fa-solid fa-shield-heart" style="font-size: 2.5rem; color:var(--teal); margin-bottom:1rem;"></i>
                    <h4 style="font-size:1.2rem; margin-bottom:.5rem; color:var(--navy);">Seguro Invaluable</h4>
                    <p style="color:var(--slate-500); font-size:.9rem;">Todas nuestras unidades cuentan con seguro de viajero de cobertura amplia por asiento.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- El Equipo -->
    <section class="section-pad">
        <div class="container text-center">
            <div class="section-label" data-aos="fade-up"><i class="fa-solid fa-circle"></i> EN BUENAS MANOS</div>
            <h2 class="section-title" data-aos="fade-up" data-aos-delay="100">Coordinadores Expertos</h2>
            <p class="section-desc" data-aos="fade-up" data-aos-delay="200" style="margin-bottom:3rem;">No estás solo. En cada viaje, un profesional te acompaña desde la salida hasta el regreso.</p>

            <div style="display:flex; justify-content:center; gap:3rem; flex-wrap:wrap;">
                <div style="text-align:center; max-width:200px;" data-aos="fade-up" data-aos-delay="0">
                    <img src="https://i.pravatar.cc/300?img=32" style="width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:1rem; border:4px solid var(--slate-100);">
                    <h5 style="color:var(--navy); font-size:1.1rem; margin-bottom:.2rem;">María Fernanda</h5>
                    <p style="color:var(--primary); font-size:.85rem; font-weight:600;">Logística y Rutas</p>
                </div>
                <div style="text-align:center; max-width:200px;" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://i.pravatar.cc/300?img=11" style="width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:1rem; border:4px solid var(--slate-100);">
                    <h5 style="color:var(--navy); font-size:1.1rem; margin-bottom:.2rem;">Carlos Rivera</h5>
                    <p style="color:var(--primary); font-size:.85rem; font-weight:600;">Coordinador General</p>
                </div>
                <div style="text-align:center; max-width:200px;" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://i.pravatar.cc/300?img=47" style="width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:1rem; border:4px solid var(--slate-100);">
                    <h5 style="color:var(--navy); font-size:1.1rem; margin-bottom:.2rem;">Ana López</h5>
                    <p style="color:var(--primary); font-size:.85rem; font-weight:600;">Atención a Clientes</p>
                </div>
            </div>
        </div>
    </section>
@endsection
