<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Mostrar Formulario de Inicio de Sesión
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Procesar Autenticación del Administrador
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo es inválido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales ingresadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar Sesión del Administrador
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }

    /**
     * Dashboard del Panel de Administración (Requiere Auth)
     */
    public function dashboard()
    {
        // Seguridad: Si no ha iniciado sesión, expulsar a login
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Redirección si el usuario es un barbero para que no vea información global
        if (Auth::user()->isBarber()) {
            return redirect()->route('barber.dashboard');
        }

        // 1. Obtener todas las citas ordenadas por fecha y hora
        $appointments = Appointment::with(['service', 'specialist'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // 2. Calcular estadísticas clave para el negocio
        $stats = [
            'total_appointments' => $appointments->count(),
            'today_appointments' => Appointment::whereDate('date', today())->count(),
            'pending_appointments' => Appointment::where('status', 'pendiente')->count(),
            // Suma del precio total de citas que no hayan sido canceladas
            'total_earnings' => Appointment::where('status', '!=', 'cancelada')->sum('total_price'),
        ];

        return view('admin.dashboard', compact('appointments', 'stats'));
    }

    /**
     * Cambiar el Estado de una Cita (Pendiente, Confirmada, Completada, Cancelada)
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Seguridad: Si no ha iniciado sesión, denegar acción
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Restricción para barberos: Solo pueden modificar sus propias citas
        if (Auth::user()->isBarber() && $appointment->specialist_id !== Auth::user()->specialist->id) {
            abort(403, 'Acción no autorizada.');
        }

        $request->validate([
            'status' => 'required|in:pendiente,confirmada,completada,cancelada',
        ]);

        $oldStatus = $appointment->status;
        
        $appointment->update([
            'status' => $request->status,
        ]);

        // Simulación Profesional de Notificaciones al pasar a Confirmada
        if ($request->status === 'confirmada' && $oldStatus !== 'confirmada') {
            $specialist = $appointment->specialist;
            $service = $appointment->service;
            
            // Números y correos (simulados)
            $barberPhone = '+56 9 ' . rand(7000, 9999) . ' ' . rand(1000, 9999);
            $barberEmail = $specialist->user ? $specialist->user->email : 'carlos@barberia.com';
            
            // Plantilla WhatsApp Barbero
            $whatsappBarber = "💈 *NOTIFICACIÓN NOBLE GROOM*\n\n"
                . "¡Hola *{$specialist->name}*! Tu cliente **{$appointment->customer_name}** ha confirmado su cita para hoy/mañana.\n\n"
                . "📅 *Fecha:* " . $appointment->date->format('d/m/Y') . "\n"
                . "🕒 *Hora:* " . substr($appointment->time, 0, 5) . " hrs\n"
                . "💇‍♂️ *Servicio:* {$service->name}\n\n"
                . "¡Prepárate para recibirlo!";
                
            // Plantilla Email Barbero (HTML Premium)
            $emailBarber = "<div style='font-family: Montserrat, sans-serif; padding: 20px; background-color: #FAF8F5; border: 1px solid #E5DCC6;'>"
                . "<h2 style='font-family: Outfit, sans-serif; font-weight: 900; color: #000; letter-spacing: -0.02em; margin-bottom: 5px; text-transform: uppercase;'>THE NOBLE GROOM</h2>"
                . "<p style='color: #D68A6B; font-weight: bold; margin-top: 0;'>¡NUEVA CITA CONFIRMADA!</p>"
                . "<p>Hola <strong>{$specialist->name}</strong>,</p>"
                . "<p>Te informamos que tu cliente <strong>{$appointment->customer_name}</strong> ha confirmado su reserva en tu jornada.</p>"
                . "<hr style='border: 0; border-top: 1px solid #E5DCC6; margin: 15px 0;'>"
                . "<table style='width: 100%; border-collapse: collapse;'>"
                . "<tr><td style='padding: 5px 0; color: #666;'><strong>📅 Fecha:</strong></td><td>" . $appointment->date->format('d/m/Y') . "</td></tr>"
                . "<tr><td style='padding: 5px 0; color: #666;'><strong>🕒 Hora:</strong></td><td>" . substr($appointment->time, 0, 5) . " hrs</td></tr>"
                . "<tr><td style='padding: 5px 0; color: #666;'><strong>💇‍♂️ Servicio:</strong></td><td>{$service->name}</td></tr>"
                . "<tr><td style='padding: 5px 0; color: #666;'><strong>📞 Cliente:</strong></td><td>{$appointment->customer_name} ({$appointment->customer_phone})</td></tr>"
                . "</table>"
                . "<hr style='border: 0; border-top: 1px solid #E5DCC6; margin: 15px 0;'>"
                . "<p style='font-size: 0.85rem; color: #777;'>Esta notificación simula un envío SMTP real en producción.</p>"
                . "</div>";

            // Plantilla WhatsApp Cliente (Confirmación de Recepción)
            $whatsappClient = "💈 *THE NOBLE GROOM*\n\n"
                . "¡Hola *{$appointment->customer_name}*! Tu cita con el barbero *{$specialist->name}* ha sido confirmada con éxito.\n\n"
                . "💇‍♂️ *Servicio:* {$service->name}\n"
                . "📅 *Fecha:* " . $appointment->date->format('d/m/Y') . "\n"
                . "🕒 *Hora:* " . substr($appointment->time, 0, 5) . " hrs\n"
                . "📍 *Ubicación:* Av. Vitacura 4500, Vitacura.\n\n"
                . "_Te sugerimos llegar 5 minutos antes. ¡Nos vemos!_";

            $request->session()->flash('notification_sent', [
                'barber_name' => $specialist->name,
                'customer_name' => $appointment->customer_name,
                'barber_phone' => $barberPhone,
                'barber_email' => $barberEmail,
                'customer_phone' => $appointment->customer_phone,
                'whatsapp_barber' => $whatsappBarber,
                'email_barber' => $emailBarber,
                'whatsapp_client' => $whatsappClient
            ]);
        }

        return back()->with('success', 'El estado de la cita se actualizó correctamente.');
    }

    /**
     * Listado de Barberos (Especialistas) para Administración
     */
    public function specialistsIndex()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $specialists = Specialist::with('user')->orderBy('name', 'asc')->get();
        return view('admin.specialists', compact('specialists'));
    }

    /**
     * Registrar un nuevo Barbero
     */
    public function storeSpecialist(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'role.required' => 'El rol o especialidad es obligatorio.',
            'bio.required' => 'La biografía es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.max' => 'La imagen no debe pesar más de 2MB.',
        ];

        if ($request->create_account === '1') {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['password'] = 'required|string|min:4';
            $messages['email.required'] = 'El correo electrónico es obligatorio para crear la cuenta.';
            $messages['email.email'] = 'El correo debe ser una dirección de email válida.';
            $messages['email.unique'] = 'Este correo electrónico ya está registrado en el sistema.';
            $messages['password.required'] = 'La contraseña es obligatoria para la cuenta.';
            $messages['password.min'] = 'La contraseña debe tener al menos 4 caracteres.';
        }

        $request->validate($rules, $messages);

        $imagePath = 'assets/barber_team_1.png'; // Por defecto

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'barber_team_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets'), $filename);
            $imagePath = 'assets/' . $filename;
        }

        $userId = null;
        if ($request->create_account === '1') {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);
            $userId = $user->id;
        }

        Specialist::create([
            'name' => $request->name,
            'role' => $request->role,
            'bio' => $request->bio,
            'image' => $imagePath,
            'user_id' => $userId,
        ]);

        return back()->with('success', 'El barbero se registró correctamente en el sistema.');
    }

    /**
     * Actualizar los datos y clave de un Barbero
     */
    public function updateSpecialist(Request $request, Specialist $specialist)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'role.required' => 'El rol o especialidad es obligatorio.',
            'bio.required' => 'La biografía es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.max' => 'La imagen no debe pesar más de 2MB.',
        ];

        // Si se marca que gestiona cuenta o si ya tenía cuenta activa
        if ($request->create_account === '1') {
            if ($specialist->user_id) {
                // Ya tiene cuenta: validar email único excluyéndose a sí mismo
                $rules['email'] = 'required|email|unique:users,email,' . $specialist->user_id;
                $rules['password'] = 'nullable|string|min:4'; // Contraseña opcional para reseteo
            } else {
                // No tiene cuenta: email y password requeridos
                $rules['email'] = 'required|email|unique:users,email';
                $rules['password'] = 'required|string|min:4';
            }
            $messages['email.required'] = 'El correo electrónico es obligatorio para la cuenta.';
            $messages['email.email'] = 'El correo debe ser una dirección de email válida.';
            $messages['email.unique'] = 'Este correo electrónico ya está registrado en el sistema.';
            $messages['password.required'] = 'La contraseña es obligatoria para crear la cuenta.';
            $messages['password.min'] = 'La contraseña debe tener al menos 4 caracteres.';
        }

        $request->validate($rules, $messages);

        // Procesamiento de Imagen
        $imagePath = $specialist->image;
        if ($request->hasFile('image')) {
            // Eliminar imagen personalizada anterior si no es la por defecto
            if ($specialist->image && $specialist->image !== 'assets/barber_team_1.png' && $specialist->image !== 'assets/barber_team_2.png') {
                $oldFullPath = public_path($specialist->image);
                if (file_exists($oldFullPath)) {
                    unlink($oldFullPath);
                }
            }

            $file = $request->file('image');
            $filename = 'barber_team_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets'), $filename);
            $imagePath = 'assets/' . $filename;
        }

        // Procesamiento de Cuenta de Acceso
        $userId = $specialist->user_id;
        if ($request->create_account === '1') {
            if ($specialist->user_id) {
                // Actualizar usuario existente
                $user = User::find($specialist->user_id);
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                if ($request->password) {
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
                }
                $user->update($userData);
            } else {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                ]);
                $userId = $user->id;
            }
        } elseif ($specialist->user_id) {
            // Si desmarca la casilla pero antes tenía cuenta, eliminar el usuario de acceso
            $oldUser = User::find($specialist->user_id);
            if ($oldUser) {
                $oldUser->delete();
            }
            $userId = null;
        }

        // Actualizar Especialista
        $specialist->update([
            'name' => $request->name,
            'role' => $request->role,
            'bio' => $request->bio,
            'image' => $imagePath,
            'user_id' => $userId,
        ]);

        return back()->with('success', 'El barbero se actualizó correctamente en el sistema.');
    }

    /**
     * Dar de baja / Eliminar un Barbero
     */
    public function destroySpecialist(Specialist $specialist)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Eliminar la foto si no es la por defecto
        if ($specialist->image && $specialist->image !== 'assets/barber_team_1.png' && $specialist->image !== 'assets/barber_team_2.png') {
            $fullPath = public_path($specialist->image);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $linkedUser = $specialist->user; // Cargar la relación antes de borrar

        $specialist->delete(); // Eliminar de la base de datos (borra citas en cascada)

        // Si tenía cuenta vinculada, eliminar el usuario de acceso
        if ($linkedUser) {
            $linkedUser->delete();
        }

        return back()->with('success', 'El barbero y todas sus citas asociadas se eliminaron correctamente.');
    }

    /**
     * Dashboard Restringido Exclusivo de Barberos
     */
    public function barberDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Verificar que efectivamente sea un barbero
        if (!Auth::user()->isBarber()) {
            return redirect()->route('admin.dashboard');
        }

        $specialist = Auth::user()->specialist;

        // Citas asignadas a este barbero ordenadas cronológicamente
        $appointments = Appointment::with('service')
            ->where('specialist_id', $specialist->id)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // Estadísticas personales del barbero
        $stats = [
            'total_appointments' => $appointments->count(),
            'today_appointments' => Appointment::where('specialist_id', $specialist->id)
                ->whereDate('date', today())->count(),
            'pending_appointments' => Appointment::where('specialist_id', $specialist->id)
                ->where('status', 'pendiente')->count(),
            'completed_appointments' => Appointment::where('specialist_id', $specialist->id)
                ->where('status', 'completada')->count(),
        ];

        return view('admin.barber_dashboard', compact('appointments', 'stats', 'specialist'));
    }
}
