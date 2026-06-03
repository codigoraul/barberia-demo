<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Specialist;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarberController extends Controller
{
    /**
     * Mostrar la Página de Inicio (Landing Page)
     */
    public function index()
    {
        $services = Service::all();
        $specialists = Specialist::all();
        
        return view('landing', compact('services', 'specialists'));
    }

    /**
     * Mostrar el Formulario de Reserva Interactivo
     */
    public function showBookingForm()
    {
        $services = Service::all();
        $specialists = Specialist::all();

        return view('booking', compact('services', 'specialists'));
    }

    /**
     * Registrar una Nueva Cita (Procesar Reserva)
     */
    public function storeBooking(Request $request)
    {
        // Validar los datos recibidos del formulario
        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'required|email|max:100',
            'customer_phone' => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'specialist_id' => 'required|exists:specialists,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
        ], [
            'customer_name.required' => 'El nombre es obligatorio.',
            'customer_email.required' => 'El correo electrónico es obligatorio.',
            'customer_email.email' => 'El formato del correo es inválido.',
            'customer_phone.required' => 'El teléfono es obligatorio.',
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'specialist_id.exists' => 'El barbero seleccionado no existe.',
            'date.after_or_equal' => 'La fecha seleccionada no puede ser anterior a hoy.',
            'time.required' => 'La hora de la cita es obligatoria.',
        ]);

        // Obtener el servicio para calcular el costo total
        $service = Service::findOrFail($validatedData['service_id']);
        
        // Crear la cita en estado "pendiente"
        $appointment = Appointment::create([
            'customer_name' => $validatedData['customer_name'],
            'customer_email' => $validatedData['customer_email'],
            'customer_phone' => $validatedData['customer_phone'],
            'service_id' => $validatedData['service_id'],
            'specialist_id' => $validatedData['specialist_id'],
            'date' => $validatedData['date'],
            'time' => $validatedData['time'],
            'total_price' => $service->price,
            'status' => 'pendiente',
        ]);

        // Cargar las relaciones para retornar el recibo completo
        $appointment->load(['service', 'specialist']);

        // Enviar correo de confirmación de recepción de cita usando la función mail() de PHP
        try {
            $emailSubject = "Reserva Recibida (Pendiente de Confirmación) - The Noble Groom";
            $emailContent = "<div style='font-family: Montserrat, sans-serif; padding: 20px; background-color: #FAF8F5; border: 1px solid #E5DCC6; max-width: 600px; margin: 0 auto;'>"
                . "<div style='text-align: center; margin-bottom: 20px;'>"
                . "<h2 style='font-family: Outfit, sans-serif; font-weight: 900; color: #000; letter-spacing: -0.02em; margin-bottom: 5px; text-transform: uppercase; margin-top: 0;'>THE NOBLE GROOM</h2>"
                . "<p style='color: #C5A880; font-weight: bold; margin-top: 0; letter-spacing: 0.15em; font-size: 0.85rem; text-transform: uppercase;'>Barbería & Peluquería Premium</p>"
                . "</div>"
                . "<h3 style='color: #000; font-family: Outfit, sans-serif; text-transform: uppercase; font-weight: 800; border-bottom: 1px solid #E5DCC6; padding-bottom: 10px;'>Cita Recibida con éxito</h3>"
                . "<p>Hola <strong>{$appointment->customer_name}</strong>,</p>"
                . "<p>Hemos recibido tu solicitud de reserva en nuestro sistema. Actualmente se encuentra en estado <strong>Pendiente de Confirmación</strong>. Tan pronto como nuestro equipo la revise y confirme, te enviaremos otro correo de confirmación definitiva.</p>"
                . "<hr style='border: 0; border-top: 1px solid #E5DCC6; margin: 15px 0;'>"
                . "<table style='width: 100%; border-collapse: collapse; margin-bottom: 15px;'>"
                . "<tr><td style='padding: 8px 0; color: #666;'><strong>💇‍♂️ Servicio:</strong></td><td style='padding: 8px 0; font-weight: bold;'>{$appointment->service->name}</td></tr>"
                . "<tr><td style='padding: 8px 0; color: #666;'><strong>💈 Barbero:</strong></td><td style='padding: 8px 0;'>{$appointment->specialist->name}</td></tr>"
                . "<tr><td style='padding: 8px 0; color: #666;'><strong>📅 Fecha:</strong></td><td style='padding: 8px 0;'><strong>" . $appointment->date->format('d/m/Y') . "</strong></td></tr>"
                . "<tr><td style='padding: 8px 0; color: #666;'><strong>🕒 Hora:</strong></td><td style='padding: 8px 0;'><strong>" . substr($appointment->time, 0, 5) . " hrs</strong></td></tr>"
                . "<tr><td style='padding: 8px 0; color: #666;'><strong>💰 Precio Total:</strong></td><td style='padding: 8px 0; color: #C5A880; font-weight: bold;'>$" . number_format($appointment->total_price, 0, ',', '.') . "</td></tr>"
                . "</table>"
                . "<hr style='border: 0; border-top: 1px solid #E5DCC6; margin: 15px 0;'>"
                . "<p style='font-size: 0.8rem; color: #999; text-align: center; margin-bottom: 0;'>© " . date('Y') . " The Noble Groom. Todos los derechos reservados.</p>"
                . "</div>";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: The Noble Groom <noreply@aplicacionweb.cl>' . "\r\n";
            
            mail($appointment->customer_email, $emailSubject, $emailContent, $headers);
        } catch (\Exception $e) {
            // Silenciar errores
        }

        // Retornar respuesta JSON para el flujo dinámico con JavaScript
        return response()->json([
            'success' => true,
            'message' => '¡Tu reserva se ha registrado con éxito!',
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'service' => $appointment->service->name,
                'specialist' => $appointment->specialist->name,
                'date' => $appointment->date->format('d/m/Y'),
                'time' => substr($appointment->time, 0, 5), // HH:MM
                'price' => number_format($appointment->total_price, 0, ',', '.'),
            ]
        ]);
    }
}
