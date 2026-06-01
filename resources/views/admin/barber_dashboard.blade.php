@extends('layouts.app')

@section('title', 'Mi Agenda de Citas | The Noble Groom')

@section('content')
  <section class="admin-dashboard-section">
    <div class="dashboard-container">
      
      <!-- Cabecera del Dashboard -->
      <div class="dashboard-header-bar">
        <div>
          <h2 class="dashboard-title">Mi Agenda Personal 📅</h2>
          <p class="dashboard-subtitle">Bienvenido, <strong>{{ $specialist->name }}</strong>. Revisa y gestiona tus reservas asignadas.</p>
        </div>
        
        <!-- Botón de Cerrar Sesión -->
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form-box">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm">Cerrar Sesión 🚪</button>
        </form>
      </div>

      <!-- Alerta de éxito al actualizar estados -->
      @if (session('success'))
        <div class="success-banner" style="background-color: var(--color-status-confirmed); color: white; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
          {{ session('success') }}
        </div>
      @endif

      <!-- Simulación de Notificaciones Recientes -->
      @if (session('notification_sent'))
        <div class="notification-simulation-card" style="background-color: var(--color-bg-cream); border: 2px solid var(--color-primary); padding: 2rem; margin-bottom: 2rem; border-radius: 0;">
          <h3 style="font-family: var(--font-heading); font-weight: 900; text-transform: uppercase; font-size: 1.25rem; margin-top: 0; margin-bottom: 1rem; color: #000; display: flex; align-items: center; gap: 0.5rem; letter-spacing: -0.02em;">
            <span>📬 Simulación de Notificaciones Multicanal Enviadas</span>
            <span style="background-color: var(--color-accent); color: #000; font-size: 0.7rem; padding: 0.2rem 0.5rem; font-weight: bold; border-radius: 0;">EXITOSO</span>
          </h3>
          <p style="font-size: 0.9rem; margin-bottom: 1.5rem; color: var(--color-text-muted); line-height: 1.5;">
            ¡Cita confirmada! Para garantizar el estándar más profesional, el sistema ha despachado notificaciones automáticas asíncronas para informarte sobre el estado de tu agenda:
          </p>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
            <!-- Notificaciones al Barbero -->
            <div style="background: white; border: 1px solid var(--color-border); padding: 1.25rem; display: flex; flex-direction: column; gap: 1.25rem;">
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📱 WhatsApp Recibido en tu Celular
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (Tu Teléfono):</strong> {{ session('notification_sent')['barber_phone'] }}</p>
                <pre style="white-space: pre-wrap; font-family: monospace; font-size: 0.8rem; background: #FAF8F5; padding: 1rem; border-left: 3px solid #25d366; margin: 0; color: #333; line-height: 1.4;">{{ session('notification_sent')['whatsapp_barber'] }}</pre>
              </div>
              
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📧 Correo Electrónico Recibido
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (Tu Email):</strong> {{ session('notification_sent')['barber_email'] }}</p>
                <div style="font-size: 0.85rem; border: 1px dashed var(--color-border); padding: 0.5rem; background: white;">
                  {!! session('notification_sent')['email_barber'] !!}
                </div>
              </div>
            </div>
            
            <!-- Notificaciones al Cliente -->
            <div style="background: white; border: 1px solid var(--color-border); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; gap: 1.25rem;">
              <div>
                <h4 style="font-family: var(--font-heading); font-weight: 900; margin-top: 0; font-size: 0.95rem; text-transform: uppercase; color: var(--color-accent); border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; letter-spacing: -0.02em;">
                  📱 WhatsApp Enviado al Cliente
                </h4>
                <p style="font-size: 0.775rem; color: #777; margin-bottom: 0.5rem;"><strong>Destinatario (Cliente):</strong> {{ session('notification_sent')['customer_phone'] }}</p>
                <pre style="white-space: pre-wrap; font-family: monospace; font-size: 0.8rem; background: #FAF8F5; padding: 1rem; border-left: 3px solid #25d366; margin: 0; color: #333; line-height: 1.4;">{{ session('notification_sent')['whatsapp_client'] }}</pre>
              </div>
              
              <div style="padding: 1rem; background: #FAF8F5; border-left: 3px solid var(--color-accent); font-size: 0.8rem; line-height: 1.5; height: fit-content;">
                <strong style="text-transform: uppercase; font-family: var(--font-heading); font-weight: 900; color: black; display: block; margin-bottom: 0.25rem;">💡 Gestión de Alto Nivel</strong>
                Este panel demuestra de forma táctil e interactiva la integración de Meta Cloud API (WhatsApp) y SMTP. El barbero queda notificado inmediatamente sin interferir con la fluidez del sistema web.
              </div>
            </div>
          </div>
        </div>
      @endif

      <!-- Grid de Estadísticas Especializadas del Barbero -->
      <div class="stats-grid">
        <!-- Tarjeta 1: Total Citas -->
        <div class="stat-card">
          <span class="stat-card-icon">📅</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['total_appointments'] }}</span>
            <span class="stat-card-label">Mis Citas Totales</span>
          </div>
        </div>

        <!-- Tarjeta 2: Citas para Hoy -->
        <div class="stat-card">
          <span class="stat-card-icon">💈</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['today_appointments'] }}</span>
            <span class="stat-card-label">Citas para Hoy</span>
          </div>
        </div>

        <!-- Tarjeta 3: Pendientes -->
        <div class="stat-card">
          <span class="stat-card-icon">⏳</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['pending_appointments'] }}</span>
            <span class="stat-card-label">Mis Citas Pendientes</span>
          </div>
        </div>

        <!-- Tarjeta 4: Completadas -->
        <div class="stat-card">
          <span class="stat-card-icon">✅</span>
          <div class="stat-card-info">
            <span class="stat-card-num">{{ $stats['completed_appointments'] }}</span>
            <span class="stat-card-label">Citas Completadas</span>
          </div>
        </div>
      </div>

      <!-- Tabla de Reservas -->
      <div class="appointments-table-box">
        <h3 class="table-section-title" style="margin-bottom: 1.5rem; color: var(--color-primary); font-family: var(--font-heading); font-size: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem;">
          📋 Mis Clientes Asignados
        </h3>
        
        @if($appointments->isEmpty())
          <div class="empty-state-box" style="text-align: center; padding: 3rem; background-color: var(--color-bg-white); border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-top: 1rem;">
            <span class="empty-state-icon" style="font-size: 3rem;">📭</span>
            <p style="margin-top: 1rem; color: var(--color-text-muted);">No tienes citas asignadas en tu agenda en este momento.</p>
          </div>
        @else
          <div class="table-responsive-wrapper">
            <table class="appointments-table">
              <thead>
                <tr>
                  <th>ID Cita</th>
                  <th>Cliente</th>
                  <th>Servicio Solicitado</th>
                  <th>Día / Hora</th>
                  <th>Monto</th>
                  <th>Mi Gestión de Estado</th>
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
                        <span class="service-duration-badge" style="background-color: var(--color-primary-light); padding: 0.25rem 0.5rem; font-size: 0.775rem; border-radius: var(--radius-full); display: inline-block; margin-top: 0.25rem;">
                          ⏱️ {{ $appointment->service->duration_minutes }} min
                        </span>
                      </div>
                    </td>
                    <td>
                      <div class="date-info-td">
                        <strong>📅 {{ $appointment->date->format('d/m/Y') }}</strong>
                        <span class="time-badge" style="background-color: var(--color-accent); color: black; font-weight: bold; padding: 0.2rem 0.5rem; font-size: 0.775rem; border-radius: var(--radius-sm); display: inline-block; margin-top: 0.25rem;">
                          🕒 {{ substr($appointment->time, 0, 5) }}
                        </span>
                      </div>
                    </td>
                    <td class="table-price-td" style="font-weight: bold;">${{ number_format($appointment->total_price, 0, ',', '.') }}</td>
                    <td>
                      <!-- Formulario para cambiar el estado de la cita asignada -->
                      <form action="{{ route('admin.appointment.status', $appointment->id) }}" method="POST" class="inline-status-form">
                        @csrf
                        <div class="status-select-wrapper">
                          <select name="status" class="status-badge-select" onchange="this.form.submit()" style="border: 2px solid var(--color-border); border-radius: var(--radius-sm); padding: 0.4rem 0.6rem; cursor: pointer; font-weight: bold;">
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
