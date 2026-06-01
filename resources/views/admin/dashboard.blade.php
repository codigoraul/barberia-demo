@extends('layouts.app')

@section('title', 'Dashboard - Agenda de Citas | The Noble Groom')

@section('content')
  <section class="admin-dashboard-section">
    <div class="dashboard-container">
      
      <!-- Cabecera del Dashboard -->
      <div class="dashboard-header-bar">
        <div>
          <h2 class="dashboard-title">Panel de Control de Agenda</h2>
          <p class="dashboard-subtitle">Bienvenido, {{ Auth::user()->name }}. Monitorea y confirma las reservas del local.</p>
        </div>
        
        <!-- Botón de Cerrar Sesión -->
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form-box">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm">Cerrar Sesión 🚪</button>
        </form>
      </div>

      <!-- Navegación del Dashboard (Pestañas) -->
      <div class="dashboard-tabs">
        <a href="{{ route('admin.dashboard') }}" class="dashboard-tab-btn active">📅 Agenda de Citas</a>
        <a href="{{ route('admin.specialists.index') }}" class="dashboard-tab-btn">💈 Gestionar Barberos</a>
      </div>

      <!-- Alerta de éxito al actualizar estados -->
      @if (session('success'))
        <div class="success-banner">
          {{ session('success') }}
        </div>
      @endif

      <!-- Simulación de Notificaciones Recientes -->
      @if (session('notification_sent'))
        <div class="notification-simulation-card" style="background-color: var(--color-bg-cream); border: 2px solid var(--color-primary); padding: 2rem; margin-bottom: 2rem; border-radius: 0;">
          <h3 style="font-family: var(--font-heading); font-weight: 900; text-transform: uppercase; font-size: 1.25rem; margin-top: 0; margin-bottom: 1rem; color: #000; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.02em;">
            <span>📬 Notificaciones Multicanal Enviadas (Simulación)</span>
            <span style="background-color: var(--color-accent); color: #000; font-size: 0.7rem; padding: 0.2rem 0.5rem; font-weight: bold; border-radius: 0;">EXITOSO</span>
          </h3>
          <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: var(--color-text-muted); line-height: 1.5;">
            ¡Cita confirmada! Para garantizar el estándar más profesional del mercado, el sistema ha desencadenado un **Job en Cola (Queue)** en segundo plano para despachar de forma asíncrona notificaciones por correo electrónico y WhatsApp de la siguiente manera:
          </p>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
            <!-- Notificaciones al Barbero -->
            <div style="background: white; border: 1px solid var(--color-border); padding: 1.25rem; display: flex; flex-direction: column; gap: 1.25rem;">
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📱 WhatsApp al Barbero ({{ session('notification_sent')['barber_name'] }})
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (WhatsApp API):</strong> {{ session('notification_sent')['barber_phone'] }}</p>
                <pre style="white-space: pre-wrap; font-family: monospace; font-size: 0.8rem; background: #FAF8F5; padding: 1rem; border-left: 3px solid #25d366; margin: 0; color: #333; line-height: 1.4;">{{ session('notification_sent')['whatsapp_barber'] }}</pre>
              </div>
              
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📧 Email al Barbero ({{ session('notification_sent')['barber_name'] }})
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (SMTP):</strong> {{ session('notification_sent')['barber_email'] }}</p>
                <div style="font-size: 0.85rem; border: 1px dashed var(--color-border); padding: 0.5rem; background: white;">
                  {!! session('notification_sent')['email_barber'] !!}
                </div>
              </div>
            </div>
            
            <!-- Notificaciones al Cliente -->
            <div style="background: white; border: 1px solid var(--color-border); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1.25rem;">
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📱 WhatsApp al Cliente (Confirmación de Reserva)
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (WhatsApp API):</strong> {{ session('notification_sent')['customer_phone'] }}</p>
                <pre style="white-space: pre-wrap; font-family: monospace; font-size: 0.8rem; background: #FAF8F5; padding: 1rem; border-left: 3px solid #25d366; margin: 0; color: #333; line-height: 1.4;">{{ session('notification_sent')['whatsapp_client'] }}</pre>
              </div>
              
              <div style="padding: 1rem; background: #FAF8F5; border-left: 3px solid var(--color-accent); font-size: 0.8rem; line-height: 1.5; height: fit-content;">
                <strong style="text-transform: uppercase; font-family: var(--font-heading); font-weight: 900; color: black; display: block; margin-bottom: 0.25rem;">💡 Estándar de Calidad Profesional</strong>
                En producción, este flujo se gestiona asíncronamente con **Laravel Queues (Redis/SQS)**. El barbero recibe la alerta instantáneamente en su teléfono sin demorar la navegación web del administrador, garantizando una excelente experiencia de usuario (CWV).
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- Grid de Estadísticas -->
      <div class="stats-grid">
        <!-- Tarjeta 1: Total Citas -->
        <div class="stat-card">
          <span class="stat-card-icon">📅</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['total_appointments'] }}</span>
            <span class="stat-card-label">Total Reservas</span>
          </div>
        </div>

        <!-- Tarjeta 2: Ganancias -->
        <div class="stat-card">
          <span class="stat-card-icon">💰</span>
          <div class="stat-card-info">
            <span class="stat-card-num">${{ number_format($stats['total_earnings'], 0, ',', '.') }}</span>
            <span class="stat-card-label">Ganancia Estimada</span>
          </div>
        </div>

        <!-- Tarjeta 3: Hoy -->
        <div class="stat-card">
          <span class="stat-card-icon">💈</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['today_appointments'] }}</span>
            <span class="stat-card-label">Citas para Hoy</span>
          </div>
        </div>

        <!-- Tarjeta 4: Pendientes -->
        <div class="stat-card">
          <span class="stat-card-icon">⏳</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['pending_appointments'] }}</span>
            <span class="stat-card-label">Citas Pendientes</span>
          </div>
        </div>
      </div>

      <!-- Tabla de Reservas -->
      <div class="appointments-table-box">
        <h3 class="table-section-title">Listado Completo de Reservas</h3>
        
        @if($appointments->isEmpty())
          <div class="empty-state-box">
            <span class="empty-state-icon">📭</span>
            <p>No se han registrado citas en el sistema todavía. ¡Las citas agendadas por los clientes aparecerán aquí de inmediato!</p>
          </div>
        @else
          <div class="table-responsive-wrapper">
            <table class="appointments-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Cliente</th>
                  <th>Servicio</th>
                  <th>Barbero / Estilista</th>
                  <th>Día / Hora</th>
                  <th>Precio</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                @foreach($appointments as $appointment)
                  <tr class="appointment-row status-{{ $appointment->status }}">
                    <td>#{{ $appointment->id }}</td>
                    <td>
                      <div class="client-info-td">
                        <strong>{{ $appointment->customer_name }}</strong>
                        <span class="client-sub-info">📞 {{ $appointment->customer_phone }}</span>
                        <span class="client-sub-info">✉️ {{ $appointment->customer_email }}</span>
                      </div>
                    </td>
                    <td>
                      <div class="service-info-td">
                        <strong>{{ $appointment->service->name }}</strong>
                        <span class="service-duration-badge">⏱️ {{ $appointment->service->duration_minutes }} min</span>
                      </div>
                    </td>
                    <td>{{ $appointment->specialist->name }}</td>
                    <td>
                      <div class="date-info-td">
                        <strong>📅 {{ $appointment->date->format('d/m/Y') }}</strong>
                        <span class="time-badge">🕒 {{ substr($appointment->time, 0, 5) }}</span>
                      </div>
                    </td>
                    <td class="table-price-td">${{ number_format($appointment->total_price, 0, ',', '.') }}</td>
                    <td>
                      <!-- Formulario en línea para actualizar el estado instantáneamente -->
                      <form action="{{ route('admin.appointment.status', $appointment->id) }}" method="POST" class="inline-status-form">
                        @csrf
                        <div class="status-select-wrapper">
                          <select name="status" class="status-badge-select" onchange="this.form.submit()">
                            <option value="pendiente" {{ $appointment->status == 'pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                            <option value="confirmada" {{ $appointment->status == 'confirmada' ? 'selected' : '' }}>✅ Confirmada</option>
                            <option value="completada" {{ $appointment->status == 'completada' ? 'selected' : '' }}>🎉 Completada</option>
                            <option value="cancelada" {{ $appointment->status == 'cancelada' ? 'selected' : '' }}>❌ Cancelada</option>
                          </select>
                        </div>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

    </div>
  </section>
@endsection
