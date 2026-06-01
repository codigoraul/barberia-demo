@extends('layouts.app')

@section('title', 'Reservar Cita | The Noble Groom')

@section('content')
  <section class="booking-section-page">
    <div class="booking-page-container">
      
      <!-- Indicador de Pasos Activos -->
      <div class="booking-steps-indicator">
        <div class="step-indicator-item active" data-step-indicator="1">
          <span class="step-num">1</span>
          <span class="step-text">Servicio</span>
        </div>
        <div class="step-indicator-line"></div>
        <div class="step-indicator-item" data-step-indicator="2">
          <span class="step-num">2</span>
          <span class="step-text">Barbero</span>
        </div>
        <div class="step-indicator-line"></div>
        <div class="step-indicator-item" data-step-indicator="3">
          <span class="step-num">3</span>
          <span class="step-text">Fecha y Hora</span>
        </div>
        <div class="step-indicator-line"></div>
        <div class="step-indicator-item" data-step-indicator="4">
          <span class="step-num">4</span>
          <span class="step-text">Contacto</span>
        </div>
      </div>

      <!-- Tarjeta del Formulario Principal -->
      <div class="booking-card">
        <form id="appointment-booking-form" class="booking-steps-form">
          @csrf
          
          <!-- PASO 1: Selección de Servicio -->
          <div class="booking-step-panel active" data-step-panel="1">
            <h2 class="step-panel-title">1. Elige tu Servicio Premium</h2>
            <p class="step-panel-subtitle">Selecciona el tratamiento que deseas de nuestra carta.</p>
            
            @php
              $groupedServices = $services->groupBy('category');
            @endphp

            <div class="services-accordion-container">
              @foreach($groupedServices as $categoryName => $servicesList)
                @php
                  $categorySlug = Str::slug($categoryName ?: 'otros');
                  $isExpanded = $loop->first; // Primera categoría se abre por defecto
                @endphp
                
                <div class="accordion-group {{ $isExpanded ? 'active' : '' }}" id="group-{{ $categorySlug }}">
                  <!-- Cabecera de Categoría -->
                  <button type="button" class="accordion-trigger" onclick="toggleAccordion('{{ $categorySlug }}')">
                    <span class="accordion-title-text">{{ $categoryName ?: 'Otros Servicios' }}</span>
                    <span class="accordion-meta-count">
                      <span class="count-badge">{{ $servicesList->count() }}</span>
                      <span class="arrow-icon">▼</span>
                    </span>
                  </button>
                  
                  <!-- Contenido del Accordion (Listado de Píldoras) -->
                  <div class="accordion-content">
                    <div class="services-pill-list">
                      @foreach($servicesList as $service)
                        <label class="service-pill-card">
                          <input type="radio" name="service_id" value="{{ $service->id }}" 
                                 data-price="{{ $service->price }}" 
                                 data-name="{{ $service->name }}"
                                 {{ request()->query('service') == $service->id ? 'checked' : '' }}
                                 {{ $loop->parent->first && $loop->first && !request()->query('service') ? 'checked' : '' }}>
                          <div class="pill-card-content">
                            <div class="pill-left">
                              <span class="pill-icon-select"></span>
                              <div class="pill-text-info">
                                <span class="pill-title">{{ $service->name }}</span>
                                <span class="pill-meta-desc">⏱️ {{ $service->duration_minutes }} min | {{ $service->description }}</span>
                              </div>
                            </div>
                            <div class="pill-right">
                              <span class="pill-price">${{ number_format($service->price, 0, ',', '.') }}</span>
                              <span class="pill-service-icon">{{ $service->icon ?: '✂' }}</span>
                            </div>
                          </div>
                        </label>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            
            <div class="step-navigation-footer">
              <div></div> <!-- Espacio vacío izquierdo -->
              <button type="button" class="btn btn-primary btn-next-step" data-next="2">Continuar</button>
            </div>
          </div>

          <!-- PASO 2: Selección de Barbero -->
          <div class="booking-step-panel" data-step-panel="2">
            <h2 class="step-panel-title">2. Selecciona a tu Barbero de Confianza</h2>
            <p class="step-panel-subtitle">Cada uno de nuestros profesionales cuenta con una especialidad artesanal.</p>
            
            <div class="specialists-booking-grid">
              @foreach($specialists as $specialist)
                <label class="barber-option-card">
                  <input type="radio" name="specialist_id" value="{{ $specialist->id }}" 
                         data-name="{{ $specialist->name }}"
                         {{ request()->query('specialist') == $specialist->id ? 'checked' : '' }}
                         {{ $loop->first && !request()->query('specialist') ? 'checked' : '' }}>
                  <div class="barber-option-content">
                    <img src="{{ asset($specialist->image) }}" alt="Foto de {{ $specialist->name }}" class="barber-option-img">
                    <div class="barber-option-info">
                      <span class="barber-option-name">{{ $specialist->name }}</span>
                      <span class="barber-option-role">{{ $specialist->role }}</span>
                      <p class="barber-option-bio">{{ $specialist->bio }}</p>
                    </div>
                  </div>
                </label>
              @endforeach
            </div>

            <div class="step-navigation-footer">
              <button type="button" class="btn btn-secondary btn-prev-step" data-prev="1">Volver</button>
              <button type="button" class="btn btn-primary btn-next-step" data-next="3">Continuar</button>
            </div>
          </div>

          <!-- PASO 3: Selección de Fecha y Hora -->
          <div class="booking-step-panel" data-step-panel="3">
            <h2 class="step-panel-title">3. Selecciona la Fecha y Hora</h2>
            <p class="step-panel-subtitle">Planifica tu visita en nuestros horarios disponibles.</p>
            
            <div class="date-time-booking-container">
              <!-- Selector de Fecha -->
              <div class="date-picker-box">
                <label for="booking-date-field" class="field-label">Elige el Día</label>
                <input type="date" id="booking-date-field" name="date" class="form-input" 
                       min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
              </div>

              <!-- Selector de Hora (Horas simulación estéticas) -->
              <div class="time-picker-box">
                <span class="field-label">Horarios Disponibles</span>
                <div class="time-slots-grid" id="time-slots-container">
                  @php
                    $horas = [
                      '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
                      '15:00', '15:30', '16:00', '16:30', '17:00', '17:30', '18:00', '18:30', '19:00', '19:30',
                      '20:00', '20:30'
                    ];
                  @endphp
                  @foreach($horas as $hora)
                    <label class="time-slot-option">
                      <input type="radio" name="time" value="{{ $hora }}" {{ $loop->first ? 'checked' : '' }}>
                      <span class="time-slot-text">{{ $hora }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="step-navigation-footer">
              <button type="button" class="btn btn-secondary btn-prev-step" data-prev="2">Volver</button>
              <button type="button" class="btn btn-primary btn-next-step" data-next="4">Continuar</button>
            </div>
          </div>

          <!-- PASO 4: Datos de Contacto & Resumen Final -->
          <div class="booking-step-panel" data-step-panel="4">
            <h2 class="step-panel-title">4. Completa tus Datos de Contacto</h2>
            <p class="step-panel-subtitle">Para enviarte la confirmación del agendamiento.</p>
            
            <div class="booking-contact-layout">
              <!-- Formulario de datos -->
              <div class="booking-fields-box">
                <div class="form-field">
                  <label for="input-customer-name" class="field-label">Nombre Completo</label>
                  <input type="text" id="input-customer-name" name="customer_name" class="form-input" placeholder="Ej. Juan Pérez" required>
                </div>
                
                <div class="form-field">
                  <label for="input-customer-email" class="field-label">Correo Electrónico</label>
                  <input type="email" id="input-customer-email" name="customer_email" class="form-input" placeholder="Ej. juan@correo.com" required>
                </div>

                <div class="form-field">
                  <label for="input-customer-phone" class="field-label">Teléfono de Contacto</label>
                  <input type="tel" id="input-customer-phone" name="customer_phone" class="form-input" placeholder="Ej. +56 9 1234 5678" required>
                </div>
              </div>

              <!-- Caja de Resumen Residente -->
              <div class="booking-summary-sidebar">
                <h3 class="summary-sidebar-title">Resumen del Servicio</h3>
                <div class="summary-sidebar-details">
                  <div class="summary-sidebar-row">
                    <span>Servicio:</span>
                    <strong id="sidebar-service-name">-</strong>
                  </div>
                  <div class="summary-sidebar-row">
                    <span>Especialista:</span>
                    <strong id="sidebar-barber-name">-</strong>
                  </div>
                  <div class="summary-sidebar-row">
                    <span>Fecha:</span>
                    <strong id="sidebar-date-val">-</strong>
                  </div>
                  <div class="summary-sidebar-row">
                    <span>Hora:</span>
                    <strong id="sidebar-time-val">-</strong>
                  </div>
                  <hr class="summary-sidebar-divider">
                  <div class="summary-sidebar-row total-sidebar-row">
                    <span>Total a Pagar:</span>
                    <strong id="sidebar-total-price" class="sidebar-price-val">$0.00</strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contenedor para mostrar posibles errores de validación del backend -->
            <div id="booking-error-message" class="error-banner" style="display: none;"></div>

            <div class="step-navigation-footer">
              <button type="button" class="btn btn-secondary btn-prev-step" data-prev="3">Volver</button>
              <button type="submit" class="btn btn-primary" id="btn-submit-booking-form">Confirmar y Agendar</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </section>

  <!-- Modal de Éxito de Cita Agendada -->
  <div id="booking-success-modal" class="modal" role="dialog" aria-labelledby="modal-booking-title" aria-hidden="true">
    <div class="modal-content">
      <span class="modal-icon">🏆</span>
      <h3 id="modal-booking-title" class="modal-title-text">¡Cita Agendada Exitosamente!</h3>
      <p class="modal-body-text">
        Estimado/a <strong id="modal-customer-name">Cliente</strong>, tu reserva ha sido guardada en nuestro sistema.
      </p>
      
      <!-- Detalle del recibo en el modal -->
      <div class="receipt-box">
        <div class="receipt-row">
          <span>Servicio contratado:</span>
          <strong id="modal-service-name">-</strong>
        </div>
        <div class="receipt-row">
          <span>Barbero / Estilista:</span>
          <strong id="modal-barber-name">-</strong>
        </div>
        <div class="receipt-row">
          <span>Día:</span>
          <strong id="modal-date-val">-</strong>
        </div>
        <div class="receipt-row">
          <span>Hora reservada:</span>
          <strong id="modal-time-val">-</strong>
        </div>
        <hr class="receipt-divider">
        <div class="receipt-row total-receipt-row">
          <span>Precio Total:</span>
          <strong id="modal-total-price" class="price-highlight">$0.00</strong>
        </div>
      </div>

      <p class="modal-body-subtext">
        Te hemos enviado un correo de confirmación. Te esperamos en la barbería con la cafetería lista para consentirte.
      </p>

      <a href="{{ route('home') }}" class="btn btn-primary btn-block">Volver al Inicio</a>
    </div>
    <div class="modal-backdrop"></div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('js/booking.js') }}"></script>
@endsection
