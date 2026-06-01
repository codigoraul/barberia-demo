@extends('layouts.app')

@section('title', 'Gestionar Barberos - Panel Admin | The Noble Groom')

@section('content')
  <section class="admin-dashboard-section">
    <div class="dashboard-container">
      
      <!-- Cabecera del Dashboard -->
      <div class="dashboard-header-bar">
        <div>
          <h2 class="dashboard-title">Panel de Control de Barberos</h2>
          <p class="dashboard-subtitle">Agrega, modifica o da de baja a los estilistas de la barbería.</p>
        </div>
        
        <!-- Botón de Cerrar Sesión -->
        <form action="{{ route('admin.logout') }}" method="POST" class="logout-form-box">
          @csrf
          <button type="submit" class="btn btn-secondary btn-sm">Cerrar Sesión 🚪</button>
        </form>
      </div>

      <!-- Navegación del Dashboard (Pestañas) -->
      <div class="dashboard-tabs">
        <a href="{{ route('admin.dashboard') }}" class="dashboard-tab-btn">📅 Agenda de Citas</a>
        <a href="{{ route('admin.specialists.index') }}" class="dashboard-tab-btn active">💈 Gestionar Barberos</a>
      </div>

      <!-- Alertas de éxito o validación -->
      @if (session('success'))
        <div class="success-banner" style="background-color: var(--color-status-confirmed); color: white; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="error-banner" style="background-color: var(--color-status-cancelled); color: white; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
          <ul style="list-style: none;">
            @foreach ($errors->all() as $error)
              <li>⚠️ {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Sección 1: Listado de Barberos Actuales -->
      <h3 class="table-section-title" style="margin-bottom: 1.5rem; color: var(--color-primary); font-family: var(--font-heading); font-size: 1.5rem;">Equipo de Barberos Actuales</h3>
      
      @if($specialists->isEmpty())
        <div class="empty-state-box" style="text-align: center; padding: 3rem; background-color: var(--color-bg-white); border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-bottom: 3rem;">
          <span class="empty-state-icon" style="font-size: 3rem;">💈</span>
          <p style="margin-top: 1rem; color: var(--color-text-muted);">No hay barberos registrados en el sistema todavía. ¡Usa el formulario de abajo para agregar uno!</p>
        </div>
      @else
        <div class="specialists-admin-grid">
          @foreach($specialists as $specialist)
            <article class="specialist-admin-card">
              <img src="{{ asset($specialist->image) }}" alt="Foto de {{ $specialist->name }}" class="specialist-admin-img">
              <div class="specialist-admin-info">
                <h4 class="specialist-admin-name">{{ $specialist->name }}</h4>
                <span class="specialist-admin-role">{{ $specialist->role }}</span>
                <p class="specialist-admin-bio">{{ $specialist->bio }}</p>
                <div style="font-size: 0.85rem; margin-block: 0.5rem 1rem; color: var(--color-accent); font-weight: bold;">
                  @if($specialist->user)
                    🔑 Acceso: {{ $specialist->user->email }}
                  @else
                    🚫 Sin cuenta de acceso
                  @endif
                </div>
                
                <div class="specialist-admin-actions" style="display: flex; gap: 0.5rem; width: 100%; margin-top: auto;">
                  <!-- Botón Editar -->
                  <button type="button" class="btn btn-secondary btn-sm" style="flex: 1; border-color: var(--color-accent); color: var(--color-accent); padding: 0.6rem 0.5rem;"
                          onclick="openEditModal({{ json_encode([
                            'id' => $specialist->id,
                            'name' => $specialist->name,
                            'role' => $specialist->role,
                            'bio' => $specialist->bio,
                            'image' => asset($specialist->image),
                            'has_account' => $specialist->user ? true : false,
                            'email' => $specialist->user ? $specialist->user->email : ''
                          ]) }})">
                    Editar ✏️
                  </button>

                  <!-- Formulario Dar de Baja -->
                  <form action="{{ route('admin.specialists.destroy', $specialist->id) }}" method="POST" style="flex: 1;"
                        onsubmit="return confirm('⚠️ ATENCIÓN: ¿Estás seguro de que deseas dar de baja a {{ $specialist->name }}? Esto borrará permanentemente todas sus citas agendadas en la base de datos de forma irreversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-sm" style="border-color: var(--color-status-cancelled); color: var(--color-status-cancelled); width: 100%; padding: 0.6rem 0.5rem;">
                      Dar de Baja 🗑️
                    </button>
                  </form>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif

      <!-- Sección 2: Formulario para Agregar Barbero -->
      <div class="admin-form-box">
        <h3 class="table-section-title" style="margin-bottom: 1.5rem; color: var(--color-primary); font-family: var(--font-heading); font-size: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem;">
          ✨ Registrar Nuevo Barbero / Estilista
        </h3>
        
        <form action="{{ route('admin.specialists.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-grid-2">
            <!-- Nombre -->
            <div class="form-field">
              <label for="input-barber-name" class="form-label">Nombre Completo</label>
              <input type="text" id="input-barber-name" name="name" class="form-input" placeholder="Ej. Marcus Vance" value="{{ old('name') }}" required>
            </div>

            <!-- Rol o Especialidad -->
            <div class="form-field">
              <label for="input-barber-role" class="form-label">Especialidad / Rol</label>
              <input type="text" id="input-barber-role" name="role" class="form-input" placeholder="Ej. Master Barber & Shave Specialist" value="{{ old('role') }}" required>
            </div>

            <!-- Biografía -->
            <div class="form-field form-group-full">
              <label for="input-barber-bio" class="form-label">Biografía Profesional</label>
              <textarea id="input-barber-bio" name="bio" class="form-textarea" placeholder="Escribe una breve reseña sobre la experiencia del barbero, su técnica de autor o su estilo..." required>{{ old('bio') }}</textarea>
            </div>

            <!-- Foto de Perfil -->
            <div class="form-field form-group-full">
              <label for="input-barber-image" class="form-label">Fotografía del Barbero</label>
              <div class="file-input-wrapper">
                <input type="file" id="input-barber-image" name="image" class="form-input" accept="image/jpeg,image/png,image/jpg,image/webp">
                <p style="font-size: 0.775rem; color: var(--color-text-muted); margin-top: 0.25rem;">Formatos admitidos: JPG, PNG, WEBP. Tamaño máximo recomendado: 2MB. Si no subes una foto, se asignará un avatar por defecto.</p>
              </div>
            </div>

            <!-- Checkbox de cuenta de acceso -->
            <div class="form-field form-group-full" style="margin-block: 1rem 0.5rem;">
              <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.75rem;">
                <input type="checkbox" id="checkbox-create-account" name="create_account" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleAccountFields(this.checked)">
                <span class="form-label" style="margin-bottom: 0; cursor: pointer; font-size: 1rem; color: var(--color-accent);">🔑 Crear cuenta de acceso para que el barbero administre su propia agenda</span>
              </label>
            </div>

            <!-- Campos de cuenta de acceso (ocultos por defecto) -->
            <div id="account-fields-box" class="form-group-full form-grid-2" style="display: none; border-top: 1px dashed var(--color-border); padding-top: 1.5rem; margin-top: 0.5rem;">
              <div class="form-field">
                <label for="input-barber-email" class="form-label">Correo Electrónico de Acceso</label>
                <input type="email" id="input-barber-email" name="email" class="form-input" placeholder="Ej. mateo@barberia.com" value="{{ old('email') }}">
              </div>
              <div class="form-field">
                <label for="input-barber-password" class="form-label">Contraseña Temporal</label>
                <input type="password" id="input-barber-password" name="password" class="form-input" placeholder="Mínimo 4 caracteres">
              </div>
            </div>
          </div>

          <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">
              Registrar Barbero 💈
            </button>
          </div>
        </form>
      </div>

      <!-- MODAL PARA EDITAR BARBERO (Esencia clara minimalista fuerte) -->
      <div id="edit-barber-modal" class="modal" role="dialog" aria-labelledby="modal-edit-title" style="display: none; position: fixed; inset: 0; z-index: 1100; align-items: center; justify-content: center; padding: 1.5rem;">
        <div class="modal-content" style="background-color: var(--color-bg-white); border: 2px solid var(--color-primary); border-radius: 0; padding: 2.5rem; max-width: 650px; width: 100%; position: relative; z-index: 2; box-shadow: var(--shadow-premium);">
          <h3 id="modal-edit-title" class="table-section-title" style="margin-bottom: 1.5rem; color: var(--color-primary); font-family: var(--font-heading); font-size: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem; text-transform: uppercase; font-weight: 900; letter-spacing: -0.02em;">
            ✏️ Editar Barbero / Estilista
          </h3>
          
          <form id="edit-barber-form" action="" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-grid-2">
              <!-- Nombre -->
              <div class="form-field">
                <label for="edit-barber-name" class="form-label">Nombre Completo</label>
                <input type="text" id="edit-barber-name" name="name" class="form-input" placeholder="Ej. Marcus Vance" required>
              </div>

              <!-- Rol o Especialidad -->
              <div class="form-field">
                <label for="edit-barber-role" class="form-label">Especialidad / Rol</label>
                <input type="text" id="edit-barber-role" name="role" class="form-input" placeholder="Ej. Master Barber" required>
              </div>

              <!-- Biografía -->
              <div class="form-field form-group-full">
                <label for="edit-barber-bio" class="form-label">Biografía Profesional</label>
                <textarea id="edit-barber-bio" name="bio" class="form-textarea" placeholder="Escribe una breve reseña..." required></textarea>
              </div>

              <!-- Foto de Perfil -->
              <div class="form-field form-group-full">
                <label for="edit-barber-image" class="form-label">Fotografía (Opcional)</label>
                <div class="file-input-wrapper" style="display: flex; gap: 1rem; align-items: center;">
                  <img id="edit-barber-preview" src="" alt="Vista previa" style="width: 60px; height: 60px; border-radius: 0; object-fit: cover; border: 2px solid var(--color-accent);">
                  <div style="flex-grow: 1;">
                    <input type="file" id="edit-barber-image" name="image" class="form-input" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <p style="font-size: 0.7rem; color: var(--color-text-muted); margin-top: 0.25rem;">Sube una nueva foto solo si deseas reemplazar la actual.</p>
                  </div>
                </div>
              </div>

              <!-- Checkbox de cuenta de acceso -->
              <div class="form-field form-group-full" style="margin-block: 1rem 0.5rem;">
                <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.75rem;">
                  <input type="checkbox" id="edit-checkbox-account" name="create_account" value="1" style="width: 18px; height: 18px; cursor: pointer;" onchange="toggleEditAccountFields(this.checked)">
                  <span class="form-label" style="margin-bottom: 0; cursor: pointer; font-size: 1rem; color: var(--color-accent);">🔑 Crear o gestionar cuenta de acceso de agenda</span>
                </label>
              </div>

              <!-- Campos de cuenta de acceso (ocultos por defecto) -->
              <div id="edit-account-fields-box" class="form-group-full form-grid-2" style="display: none; border-top: 1px dashed var(--color-border); padding-top: 1.5rem; margin-top: 0.5rem;">
                <div class="form-field">
                  <label for="edit-barber-email" class="form-label">Correo Electrónico de Acceso</label>
                  <input type="email" id="edit-barber-email" name="email" class="form-input" placeholder="Ej. mateo@barberia.com">
                </div>
                <div class="form-field">
                  <label for="edit-barber-password" class="form-label">Nueva Contraseña (Clave)</label>
                  <input type="password" id="edit-barber-password" name="password" class="form-input" placeholder="Dejar vacío para mantener la actual">
                </div>
              </div>
            </div>

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
              <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditModal()">Cancelar</button>
              <button type="submit" class="btn btn-primary btn-sm">Guardar Cambios 💾</button>
            </div>
          </form>
        </div>
        <div class="modal-backdrop" onclick="closeEditModal()" style="position: absolute; inset: 0; background-color: rgba(0, 0, 0, 0.6); z-index: 1;"></div>
      </div>

    </div>
  </section>
@endsection

@section('scripts')
  <script>
    function toggleAccountFields(show) {
      const fieldsBox = document.getElementById('account-fields-box');
      const emailInput = document.getElementById('input-barber-email');
      const passwordInput = document.getElementById('input-barber-password');
      
      if (show) {
        fieldsBox.style.display = 'grid';
        emailInput.required = true;
        passwordInput.required = true;
      } else {
        fieldsBox.style.display = 'none';
        emailInput.required = false;
        passwordInput.required = false;
        emailInput.value = '';
        passwordInput.value = '';
      }
    }

    function toggleEditAccountFields(show) {
      const fieldsBox = document.getElementById('edit-account-fields-box');
      const emailInput = document.getElementById('edit-barber-email');
      const passwordInput = document.getElementById('edit-barber-password');
      
      if (show) {
        fieldsBox.style.display = 'grid';
        emailInput.required = true;
      } else {
        fieldsBox.style.display = 'none';
        emailInput.required = false;
        emailInput.value = '';
        passwordInput.value = '';
      }
    }

    function openEditModal(barber) {
      const modal = document.getElementById('edit-barber-modal');
      const form = document.getElementById('edit-barber-form');
      
      // Establecer ruta dinámicamente usando el helper de rutas de Laravel
      form.action = "{{ route('admin.specialists.update', ':id') }}".replace(':id', barber.id);
      
      // Poblar campos
      document.getElementById('edit-barber-name').value = barber.name;
      document.getElementById('edit-barber-role').value = barber.role;
      document.getElementById('edit-barber-bio').value = barber.bio;
      document.getElementById('edit-barber-preview').src = barber.image;
      
      // Campos de cuenta
      const checkbox = document.getElementById('edit-checkbox-account');
      checkbox.checked = barber.has_account;
      
      toggleEditAccountFields(barber.has_account);
      
      if (barber.has_account) {
        document.getElementById('edit-barber-email').value = barber.email;
      } else {
        document.getElementById('edit-barber-email').value = '';
      }
      
      // Mostrar modal
      modal.style.display = 'flex';
    }

    function closeEditModal() {
      document.getElementById('edit-barber-modal').style.display = 'none';
    }
    
    // Restaurar estado si hay error de validación anterior
    document.addEventListener('DOMContentLoaded', () => {
      const checkbox = document.getElementById('checkbox-create-account');
      if (checkbox && checkbox.checked) {
        toggleAccountFields(true);
      }
    });
  </script>
@endsection
