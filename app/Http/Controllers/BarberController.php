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
