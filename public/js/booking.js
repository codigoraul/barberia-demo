/* -------------------------------------------------------------
   The Noble Groom - Barbería Premium - Lógica de Reserva (JS)
   ------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {

  const bookingForm = document.getElementById('appointment-booking-form');
  if (!bookingForm) return; // Salir si no estamos en la página de reservas

  // Elementos de la interfaz
  const panels = document.querySelectorAll('.booking-step-panel');
  const indicatorItems = document.querySelectorAll('.step-indicator-item');
  const indicatorLines = document.querySelectorAll('.step-indicator-line');
  
  // Elementos del Resumen de Compra (Paso 4)
  const sidebarService = document.getElementById('sidebar-service-name');
  const sidebarBarber = document.getElementById('sidebar-barber-name');
  const sidebarDate = document.getElementById('sidebar-date-val');
  const sidebarTime = document.getElementById('sidebar-time-val');
  const sidebarPrice = document.getElementById('sidebar-total-price');

  // Elementos del Modal de Éxito
  const successModal = document.getElementById('booking-success-modal');
  const modalCustomerName = document.getElementById('modal-customer-name');
  const modalServiceName = document.getElementById('modal-service-name');
  const modalBarberName = document.getElementById('modal-barber-name');
  const modalDateVal = document.getElementById('modal-date-val');
  const modalTimeVal = document.getElementById('modal-time-val');
  const modalTotalPrice = document.getElementById('modal-total-price');
  
  const errorBanner = document.getElementById('booking-error-message');

  // ==========================================
  // 1. NAVEGACIÓN ENTRE PASOS (MULTI-PASO)
  // ==========================================
  
  const irAlPaso = (numeroPaso) => {
    // 1. Ocultar todos los paneles y quitar clases activas
    panels.forEach(panel => panel.classList.remove('active'));
    
    // 2. Activar el panel del paso actual
    const panelActual = document.querySelector(`.booking-step-panel[data-step-panel="${numeroPaso}"]`);
    if (panelActual) panelActual.classList.add('active');

    // 3. Actualizar la barra de indicadores visuales superiores
    indicatorItems.forEach(item => {
      const stepVal = parseInt(item.getAttribute('data-step-indicator'));
      if (stepVal < numeroPaso) {
        item.classList.remove('active');
        item.classList.add('completed');
      } else if (stepVal === numeroPaso) {
        item.classList.remove('completed');
        item.classList.add('active');
      } else {
        item.classList.remove('completed', 'active');
      }
    });

    // Actualizar líneas conectores
    indicatorLines.forEach(line => {
      const lineVal = parseInt(line.previousElementSibling.getAttribute('data-step-indicator'));
      if (lineVal < numeroPaso) {
        line.classList.add('active');
      } else {
        line.classList.remove('active');
      }
    });

    // Actualizar el resumen dinámico cada vez que el usuario avanza de paso
    actualizarResumenDinámico();
  };

  // Escuchar botones de "Continuar" (Siguiente Paso)
  document.querySelectorAll('.btn-next-step').forEach(btn => {
    btn.addEventListener('click', () => {
      const siguientePaso = btn.getAttribute('data-next');
      irAlPaso(parseInt(siguientePaso));
      window.scrollTo({ top: 150, behavior: 'smooth' }); // Desplazar arriba por comodidad
    });
  });

  // Escuchar botones de "Volver" (Paso Anterior)
  document.querySelectorAll('.btn-prev-step').forEach(btn => {
    btn.addEventListener('click', () => {
      const pasoAnterior = btn.getAttribute('data-prev');
      irAlPaso(parseInt(pasoAnterior));
      window.scrollTo({ top: 150, behavior: 'smooth' });
    });
  });


  // ==========================================
  // 2. ACTUALIZACIÓN DINÁMICA DEL RESUMEN
  // ==========================================
  
  const actualizarResumenDinámico = () => {
    // 1. Obtener Servicio Seleccionado
    const serviceRadio = bookingForm.querySelector('input[name="service_id"]:checked');
    if (serviceRadio) {
      const serviceName = serviceRadio.getAttribute('data-name');
      const servicePrice = parseFloat(serviceRadio.getAttribute('data-price'));
      sidebarService.textContent = serviceName;
      sidebarPrice.textContent = `$${servicePrice.toLocaleString('es-CL')}`;
    }

    // 2. Obtener Barbero Seleccionado
    const specialistRadio = bookingForm.querySelector('input[name="specialist_id"]:checked');
    if (specialistRadio) {
      const specialistName = specialistRadio.getAttribute('data-name');
      sidebarBarber.textContent = specialistName;
    }

    // 3. Obtener Fecha Seleccionada
    const dateInput = document.getElementById('booking-date-field').value;
    if (dateInput) {
      // Formatear fecha de YYYY-MM-DD a DD/MM/YYYY
      const partes = dateInput.split('-');
      if (partes.length === 3) {
        sidebarDate.textContent = `${partes[2]}/${partes[1]}/${partes[0]}`;
      } else {
        sidebarDate.textContent = dateInput;
      }
    }

    // 4. Obtener Hora Seleccionada
    const timeRadio = bookingForm.querySelector('input[name="time"]:checked');
    if (timeRadio) {
      sidebarTime.textContent = timeRadio.value;
    }
  };

  // Escuchar clics en opciones para actualizar resumen al instante
  bookingForm.addEventListener('change', actualizarResumenDinámico);
  actualizarResumenDinámico(); // Ejecutar carga inicial


  // ==========================================
  // 3. PROCESAMIENTO ASÍNCRONO DE LA RESERVA (FETCH JSON)
  // ==========================================
  
  bookingForm.addEventListener('submit', (e) => {
    e.preventDefault(); // Detener recarga de página por defecto
    
    // Ocultar banner de errores anteriores
    errorBanner.style.display = 'none';
    errorBanner.textContent = '';
    
    const submitBtn = document.getElementById('btn-submit-booking-form');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Procesando tu cita... ⏳';

    // Recopilar todos los datos del formulario
    const formData = new FormData(bookingForm);

    // Enviar petición POST asíncrona hacia Laravel a la misma ruta actual de navegación
    fetch(window.location.pathname, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest', // Indica que es petición AJAX
        'Accept': 'application/json' // Solicita respuesta JSON
      },
      body: formData
    })
    .then(response => {
      return response.text().then(text => {
        try {
          const data = JSON.parse(text);
          if (!response.ok) {
            throw new Error(data.message || 'Ocurrió un error al procesar tu cita. Inténtalo nuevamente.');
          }
          return data;
        } catch (jsonError) {
          console.error("Respuesta no JSON del servidor:", text);
          if (text.includes("open_basedir") || text.includes("Warning")) {
            throw new Error("El servidor tiene un conflicto de seguridad (Warning: open_basedir). Por favor, asegúrate de aplicar la solución 1 o 2 en tu cPanel explicada en el chat.");
          }
          throw new Error('El servidor devolvió una respuesta inválida (no JSON). Esto puede ser por un error en la base de datos o un warning de PHP en producción. Revisa los registros en cPanel.');
        }
      });
    })
    .then(data => {
      if (data.success) {
        // 1. Inyectar datos en el Modal de Éxito
        modalCustomerName.textContent = data.appointment.customer_name;
        modalServiceName.textContent = data.appointment.service;
        modalBarberName.textContent = data.appointment.specialist;
        modalDateVal.textContent = data.appointment.date;
        modalTimeVal.textContent = data.appointment.time;
        modalTotalPrice.textContent = `$${data.appointment.price}`;

        // 2. Abrir el Modal de Éxito
        successModal.classList.add('open');
        successModal.setAttribute('aria-hidden', 'false');
        
        // 3. Resetear formulario
        bookingForm.reset();
      }
    })
    .catch(error => {
      // Mostrar el error en la franja roja de la interfaz
      errorBanner.textContent = error.message;
      errorBanner.style.display = 'block';
      
      // Regresar los botones a su estado activo
      submitBtn.disabled = false;
      submitBtn.textContent = 'Confirmar y Agendar';
      
      // Desplazar la pantalla hasta el aviso de error para que lo vea
      errorBanner.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });

});

// Función global para expandir/colapsar categorías del catálogo de servicios
function toggleAccordion(slug) {
  const group = document.getElementById('group-' + slug);
  if (group) {
    group.classList.toggle('active');
  }
}
