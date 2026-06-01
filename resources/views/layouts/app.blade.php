<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'The Noble Groom | Barbería & Peluquería Premium')</title>
  <meta name="description" content="The Noble Groom - Barbería y peluquería boutique de alta gama. Cuidado personal masculino, cortes clásicos, modernos y perfilados de barba premium.">
  
  <!-- Tipografías de Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  <!-- Hoja de Estilos -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @yield('styles')
</head>
<body>

  <!-- Cabecera / Header Translúcido -->
  <header id="main-header" class="header-glass">
    <div class="header-container">
      <a href="{{ route('home') }}" class="logo">
        <span class="brand-accent">THE NOBLE</span> GROOM
      </a>
      
      <button class="mobile-nav-toggle" id="nav-toggle" aria-label="Abrir menú de navegación" aria-expanded="false">
        <span class="hamburger"></span>
      </button>

      <nav class="nav-menu" id="nav-menu">
        <ul class="nav-list">
          @auth
            <!-- Menú cuando hay sesión iniciada (Staff) -->
            @if(Auth::user()->isBarber())
              <li><a href="{{ route('barber.dashboard') }}" class="nav-link {{ Route::is('barber.dashboard') ? 'active' : '' }}">📅 Mi Agenda</a></li>
            @else
              <li><a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">📅 Agenda</a></li>
              <li><a href="{{ route('admin.specialists.index') }}" class="nav-link {{ Route::is('admin.specialists.index') ? 'active' : '' }}">💈 Barberos</a></li>
            @endif
            <li>
              <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="opacity: 0.8;">
                Cerrar Sesión 🚪
              </a>
              <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </li>
          @else
            <!-- Menú Público para Clientes -->
            <li><a href="{{ route('home') }}#hero" class="nav-link active">Inicio</a></li>
            <li><a href="{{ route('home') }}#servicios" class="nav-link">Servicios</a></li>
            <li><a href="{{ route('home') }}#equipo" class="nav-link">Barberos</a></li>
            <li><a href="{{ route('booking.form') }}" class="nav-link">Reservar</a></li>
          @endauth
        </ul>
      </nav>
      
      <div class="header-actions">
        @auth
          <span class="user-badge" style="font-weight: bold; font-family: var(--font-heading); font-size: 0.8rem; border: 2px solid var(--color-primary); padding: 0.5rem 1rem; background-color: var(--color-bg-cream); text-transform: uppercase; letter-spacing: 0.05em;">
            👤 {{ Auth::user()->name }}
          </span>
        @else
          <a href="{{ route('booking.form') }}" class="btn btn-primary btn-sm">Agendar Cita</a>
        @endauth
      </div>
    </div>
  </header>

  <!-- Contenido Dinámico de las Vistas -->
  <main>
    @yield('content')
  </main>

  <!-- Pie de Página -->
  <footer class="footer-section">
    <div class="footer-grid">
      <div class="footer-brand-col">
        <a href="{{ route('home') }}" class="footer-logo"><span class="brand-accent">THE NOBLE</span> GROOM</a>
        <p class="footer-tag">El club de caballeros donde el cuidado personal se encuentra con el lujo clásico y moderno.</p>
        <div class="social-links">
          <a href="#" class="social-icon" aria-label="Instagram">📸</a>
          <a href="#" class="social-icon" aria-label="Facebook">📘</a>
          <a href="#" class="social-icon" aria-label="WhatsApp">💬</a>
        </div>
      </div>

      <div class="footer-links-col">
        <h4 class="footer-heading">Navegación</h4>
        <ul class="footer-links">
          <li><a href="{{ route('home') }}#hero">Inicio</a></li>
          <li><a href="{{ route('home') }}#servicios">Servicios</a></li>
          <li><a href="{{ route('home') }}#equipo">Barberos</a></li>
          <li><a href="{{ route('booking.form') }}">Reservar Cita</a></li>
          @guest
            <li><a href="{{ route('admin.login') }}" style="opacity: 0.6; font-size: 0.8rem; border-top: 1px dashed var(--color-border); padding-top: 0.5rem; display: block; margin-top: 0.5rem;">🔑 Acceso Staff</a></li>
          @endguest
        </ul>
      </div>

      <div class="footer-contact-col">
        <h4 class="footer-heading">Contacto & Horarios</h4>
        <ul class="contact-info">
          <li>📍 Av. Vitacura 5420, Vitacura, Santiago</li>
          <li>📞 +56 2 2892 0192</li>
          <li>✉️ info@thenoblegroom.cl</li>
          <li>🕒 Lun - Sáb: 9:00 AM - 9:00 PM</li>
        </ul>
      </div>

      <div class="footer-map-col">
        <h4 class="footer-heading">Nuestra Casa</h4>
        <div class="map-widget">
          <div class="map-placeholder">
            <span class="map-pin">📍</span>
            <div class="map-details">
              <strong>The Noble Groom Boutique</strong>
              <span>Zona Exclusiva - Cafetería & Bar integrado gratis</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; {{ date('Y') }} The Noble Groom. Todos los derechos reservados. Cuidado Premium de Caballeros.</p>
    </div>
  </footer>

  <script>
    // Cabecera compacta al hacer scroll
    document.addEventListener('DOMContentLoaded', () => {
      const header = document.getElementById('main-header');
      window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
          header.classList.add('scrolled');
        } else {
          header.classList.remove('scrolled');
        }
      });

      // Menú móvil hamburguesa
      const navToggle = document.getElementById('nav-toggle');
      const navMenu = document.getElementById('nav-menu');
      if(navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
          const abierto = navToggle.getAttribute('aria-expanded') === 'true';
          navToggle.setAttribute('aria-expanded', !abierto);
          navMenu.classList.toggle('open');
        });
      }
    });
  </script>
  @yield('scripts')
</body>
</html>
