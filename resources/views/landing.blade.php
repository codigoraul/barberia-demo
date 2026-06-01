@extends('layouts.app')

@section('title', 'Inicio | The Noble Groom - Barbería Urbana Premium')

@section('content')
  <!-- Sección Hero (Portada) -->
  <section id="hero" class="hero-section">
    <div class="hero-container">
      <div class="hero-content">
        <span class="hero-tagline">Estilo Callejero & Actitud Urbana</span>
        <h1 class="hero-title">
          <span class="hero-title-text">
            Saca tu mejor versión:<br>
            Estilo, Degradados y Flow<br>
            al Máximo
          </span>
        </h1>
        <p class="hero-description">
          <span class="hero-description-text">En <strong>The Noble Groom</strong> no hacemos cortes comunes; diseñamos tu identidad. Somos el epicentro del estilo urbano y el flow en Santiago. Conéctate con nuestro crew de barberos de autor, disfruta de buena música, bebidas heladas de cortesía y sal con la facha al 100%.</span>
        </p>
        <div class="hero-actions">
          <a href="{{ route('booking.form') }}" class="btn btn-primary">Agendar al Toque</a>
          <a href="#servicios" class="btn btn-secondary">Ver los Cortes</a>
        </div>
        
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-number">4.9</span>
            <span class="stat-label">★ Google Reviews (450+)</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">250+</span>
            <span class="stat-label">Cortes con Flow</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">Gratis</span>
            <span class="stat-label">Barber Bar & Buena Música</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección de Servicios (Menú) -->
  <section id="servicios" class="services-section">
    <div class="section-header">
      <span class="section-subtitle">El Menú del Flow</span>
      <h2 class="section-title">Estilos que Detonan tu Facha</h2>
      <p class="section-desc">
        Diseños precisos, desvanecidos al milímetro y tratamientos finos para marcar la diferencia en la calle.
      </p>
    </div>

    <div class="services-grid">
      @foreach($services as $service)
        <article class="service-card">
          <div class="service-content">
            <h3 class="service-title-card">{{ $service->name }}</h3>
            <p class="service-text">{{ $service->description }}</p>
            <div class="service-meta">
              <span class="service-duration">⏱️ {{ $service->duration_minutes }} minutos</span>
            </div>
            <div class="service-footer">
              <span class="service-price">${{ number_format($service->price, 0, ',', '.') }}</span>
              <a href="{{ route('booking.form') }}?service={{ $service->id }}" class="service-link">
                Reservar <span class="arrow">&rarr;</span>
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </section>

  <!-- Sección de Barberos / Especialistas -->
  <section id="equipo" class="team-section">
    <div class="section-header">
      <span class="section-subtitle">El Crew del Flow</span>
      <h2 class="section-title">Los Reyes del Degradado y la Facha</h2>
      <p class="section-desc">
        Un crew de barberos y estilistas de autor dedicados a dejar tu corte en el nivel más alto. Pura técnica, actitud y precisión.
      </p>
    </div>

    <div class="team-grid">
      @foreach($specialists as $specialist)
        <article class="team-card">
          <div class="team-image-wrapper">
            <img src="{{ asset($specialist->image) }}" alt="Fotografía del barbero profesional {{ $specialist->name }} de The Noble Groom" class="team-image">
          </div>
          <div class="team-info">
            <h3 class="team-name">{{ $specialist->name }}</h3>
            <span class="team-role">{{ $specialist->role }}</span>
            <p class="team-bio">{{ $specialist->bio }}</p>
            <div class="team-action">
              <a href="{{ route('booking.form') }}?specialist={{ $specialist->id }}" class="btn btn-secondary btn-sm">
                Agendar con {{ explode(' ', $specialist->name)[0] }}
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </section>

  <!-- Sección del Club / Beneficios -->
  <section class="club-section">
    <div class="club-container">
      <div class="club-content">
        <span class="section-subtitle">Mucho más que una barbería</span>
        <h2 class="club-title">La Experiencia Noble Groom</h2>
        <p class="club-desc">
          Acá no solo vienes a cortarte el pelo, vienes a pasar el rato con los tuyos. Conéctate a las consolas, disfruta del mejor playlist urbano y tómate unas cervezas heladas o bebidas de cortesía al toque. Todo incluido en tu corte.
        </p>
        <div class="club-bullets">
          <div class="club-bullet">
            <span class="bullet-check">✓</span>
            <span>Estacionamiento gratis y seguro para clientes</span>
          </div>
          <div class="club-bullet">
            <span class="bullet-check">✓</span>
            <span>Cerveza helada y bebidas gratis al toque</span>
          </div>
          <div class="club-bullet">
            <span class="bullet-check">✓</span>
            <span>Sillones ergonómicos premium de máximo confort</span>
          </div>
        </div>
      </div>
      <div class="club-visual">
        <div class="visual-decor"></div>
      </div>
    </div>
  </section>
@endsection
