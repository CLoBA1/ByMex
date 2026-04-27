document.addEventListener('DOMContentLoaded', () => {
    // 1. Init AOS
    AOS.init({
        duration: 800,
        easing: 'ease-out-cubic',
        once: true,
        offset: 60,
        delay: 0,
    });

    // 2. Navbar scroll effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // 3. Animated stat counters
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const obs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count);
                    const suffix = el.dataset.suffix || '';
                    const prefix = el.dataset.prefix || '';
                    const duration = 2200;
                    const steps = 60;
                    const increment = target / steps;
                    let current = 0;
                    let step = 0;
                    const timer = setInterval(() => {
                        step++;
                        // Ease-out effect
                        const progress = 1 - Math.pow(1 - step / steps, 3);
                        current = Math.round(target * progress);
                        el.textContent = prefix + current.toLocaleString('es-MX') + suffix;
                        if (step >= steps) {
                            el.textContent = prefix + target.toLocaleString('es-MX') + suffix;
                            clearInterval(timer);
                        }
                    }, duration / steps);
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(c => obs.observe(c));
    }

    // 4. Parallax hero
    const hero = document.querySelector('.hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            if (window.scrollY < window.innerHeight) {
                hero.style.backgroundPositionY = `${window.scrollY * 0.35}px`;
            }
        });
    }

    // 5. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('href');
            if (id === '#') return;
            const target = document.querySelector(id);
            if (target) {
                const offset = 80;
                window.scrollTo({
                    top: target.getBoundingClientRect().top + window.pageYOffset - offset,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 6. Init Swiper if testimonials slider exists
    if (document.querySelector('.testimonials-swiper')) {
        new Swiper('.testimonials-swiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            },
        });
    }
});
