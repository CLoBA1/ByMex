<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Viajes By Mex — Tu agencia de viajes y excursiones premium en México. Autobuses de lujo, hospedaje 4 estrellas y experiencias inolvidables.">
    <title>Viajes By Mex | @yield('title', 'Descubre México')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- AOS Animate On Scroll -->
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
    
    <!-- Swiper Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=1.2">
    @yield('extra-css')
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('img/logobymex.jpeg') }}" alt="Viajes By Mex Logo">
            </a>
            
            <div class="nav-links" id="mobile-nav-links">
                <a href="{{ route('home') }}" onclick="document.getElementById('mobile-nav-links').classList.remove('mobile-active')">Inicio</a>
                <a href="{{ route('about') }}" onclick="document.getElementById('mobile-nav-links').classList.remove('mobile-active')">Nosotros</a>
                <a href="{{ route('tours.index') }}" onclick="document.getElementById('mobile-nav-links').classList.remove('mobile-active')">Destinos</a>
                <a href="{{ route('home') }}#testimonials" onclick="document.getElementById('mobile-nav-links').classList.remove('mobile-active')">Testimonios</a>
            </div>
            
            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline" style="font-size:.82rem;padding:.55rem 1.15rem"><i class="fa-solid fa-gauge-high"></i> Panel</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline" style="border:none;padding:.5rem;font-size:1rem"><i class="fa-solid fa-user"></i></a>
                @endauth
                <a href="{{ route('tours.index') }}" class="btn btn-primary btn-cta-nav">Explorar <i class="fa-solid fa-arrow-right"></i></a>
                <button class="mobile-menu-btn" onclick="document.getElementById('mobile-nav-links').classList.toggle('mobile-active')">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-brand">
                <img src="{{ asset('img/logobymex.jpeg') }}" alt="Logo">
                <p>Tu agencia de viajes y excursiones de confianza. Creamos experiencias que tienes que vivir.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="{{ route('home') }}">Inicio</a></li>
                    <li><a href="{{ route('about') }}">Nosotros</a></li>
                    <li><a href="{{ route('tours.index') }}">Próximos Viajes</a></li>
                    <li><a href="#">Políticas de Cancelación</a></li>
                    <li><a href="#">Preguntas Frecuentes</a></li>
                </ul>
            </div>
            <div class="footer-contact">
                <h3>Contacto</h3>
                <p><i class="fa-solid fa-phone"></i> 744 129 5026</p>
                <p><i class="fa-solid fa-phone"></i> 733 136 2024</p>
                <p><i class="fa-solid fa-envelope"></i> info@viajesbymex.com</p>
                <p><i class="fa-solid fa-location-dot"></i> Salidas desde Iguala y alrededores</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Viajes By Mex. Todos los derechos reservados. Hecho con <i class="fa-solid fa-heart" style="color:var(--primary)"></i> en México.</p>
        </div>
    </footer>

    <!-- WhatsApp Float -->
    <a href="https://wa.me/527441295026?text=Hola%2C%20quiero%20información%20sobre%20los%20próximos%20viajes" target="_blank" class="whatsapp-float" aria-label="WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <!-- AOS Init -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <!-- Swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('extra-js')
</body>
</html>
