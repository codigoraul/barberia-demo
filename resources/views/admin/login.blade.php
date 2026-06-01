@extends('layouts.app')

@section('title', 'Acceso Administrativo | The Noble Groom')

@section('content')
  <section class="admin-login-section">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <span class="brand-accent">THE NOBLE</span> GROOM
          <h2 class="login-title">Control de Agenda</h2>
          <p class="login-subtitle">Ingresa al panel administrativo de citas.</p>
        </div>

        <!-- Alerta de errores de validación de Laravel -->
        @if ($errors->any())
          <div class="error-banner">
            <ul class="error-list">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="login-form">
          @csrf
          
          <div class="form-field">
            <label for="input-email" class="field-label">Correo Electrónico</label>
            <input type="email" id="input-email" name="email" class="form-input" 
                   value="{{ old('email', 'admin@barberia.com') }}" placeholder="ejemplo@barberia.com" required>
          </div>

          <div class="form-field">
            <label for="input-password" class="field-label">Contraseña</label>
            <input type="password" id="input-password" name="password" class="form-input" 
                   value="admin" placeholder="••••••••" required>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>

        <!-- Recordatorio de credenciales de prueba -->
        <div class="test-credentials-box">
          <strong>🔑 Acceso de Prueba Precargado:</strong>
          <ul>
            <li>Usuario: <code>admin@barberia.com</code></li>
            <li>Contraseña: <code>admin</code></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
@endsection
