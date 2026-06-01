<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\Specialist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear usuario Administrador para el panel privado
        User::updateOrCreate(
            ['email' => 'admin@barberia.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin'),
            ]
        );

        // 2. Crear Catálogo de Servicios Agrupados
        $servicios = [
            // Categoria: Promociones
            [
                'name' => 'Corte de Pelo + Lavado',
                'description' => 'Servicio combinado de corte clásico o moderno más lavado profundo con masaje capilar relajante.',
                'price' => 16000,
                'duration_minutes' => 45,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Corte de Pelo + Barba',
                'description' => 'Corte clásico/tendencia más perfilado de barba simple con navaja libre y aceites esenciales.',
                'price' => 23000,
                'duration_minutes' => 60,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Corte de Pelo + Barba + Lavado',
                'description' => 'El combo ideal: corte premium, perfilado de barba a navaja y lavado con masaje relajante.',
                'price' => 24000,
                'duration_minutes' => 70,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Corte de Pelo + Masaje Capilar',
                'description' => 'Corte de cabello más tratamiento intensivo de masaje capilar con aceites naturales.',
                'price' => 20000,
                'duration_minutes' => 55,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Corte de Pelo + Barba + Masaje',
                'description' => 'Servicio completo: corte estilizado, perfilado de barba, lavado y masaje capilar premium.',
                'price' => 28000,
                'duration_minutes' => 80,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Barba + Limpieza Facial Normal',
                'description' => 'Tratamiento facial rápido más perfilado de barba completo con toalla caliente.',
                'price' => 17000,
                'duration_minutes' => 50,
                'category' => 'Promociones',
                'icon' => '✂',
            ],
            [
                'name' => 'Experiencia Barbudos',
                'description' => 'Corte premium + perfilado de barba + cejas + masaje capilar intensivo y toalla caliente de relajación.',
                'price' => 45000,
                'duration_minutes' => 100,
                'category' => 'Promociones',
                'icon' => '⭐',
            ],
            
            // Categoria: Limpiezas Faciales
            [
                'name' => 'Limpieza Facial Normal',
                'description' => 'Tratamiento express de exfoliación, mascarilla purificante y crema hidratante.',
                'price' => 8000,
                'duration_minutes' => 30,
                'category' => 'Limpiezas Faciales',
                'icon' => '😊',
            ],
            [
                'name' => 'Limpieza Facial Premium',
                'description' => 'Exfoliación profunda con café orgánico, mascarilla de carbón activo, vapor y masaje de drenaje facial.',
                'price' => 12000,
                'duration_minutes' => 45,
                'category' => 'Limpiezas Faciales',
                'icon' => '😊',
            ],

            // Categoria: Servicios Individuales
            [
                'name' => 'Corte de Pelo',
                'description' => 'Corte estilizado clásico o fade urbano a máquina y tijeras en parte superior, incluye lavado.',
                'price' => 15000,
                'duration_minutes' => 40,
                'category' => 'Servicios Individuales',
                'icon' => '✂',
            ],
            [
                'name' => 'Perfilado de Barba',
                'description' => 'Recorte, alineado y afeitado de barba express con aplicación de lociones hidratantes.',
                'price' => 11000,
                'duration_minutes' => 25,
                'category' => 'Servicios Individuales',
                'icon' => '✂',
            ],
            [
                'name' => 'Perfilado de Ceja',
                'description' => 'Diseño y limpieza de cejas con navaja y pinzas para un marco facial perfecto.',
                'price' => 3000,
                'duration_minutes' => 15,
                'category' => 'Servicios Individuales',
                'icon' => '😊',
            ],
            [
                'name' => 'Lavado de Cabello',
                'description' => 'Lavado profundo con champú premium revitalizante y peinado final.',
                'price' => 3000,
                'duration_minutes' => 15,
                'category' => 'Servicios Individuales',
                'icon' => '😊',
            ],
            [
                'name' => 'Masaje Capilar',
                'description' => 'Tratamiento y masaje capilar estimulante para la salud del cuero cabelludo.',
                'price' => 5000,
                'duration_minutes' => 20,
                'category' => 'Servicios Individuales',
                'icon' => '💆‍♂️',
            ],
        ];

        foreach ($servicios as $serv) {
            Service::updateOrCreate(
                ['name' => $serv['name']],
                $serv
            );
        }

        // 3. Crear Especialistas (Barberos) con sus cuentas de acceso asociadas
        
        // Crear Cuenta para Carlos
        $carlosUser = User::updateOrCreate(
            ['email' => 'carlos@barberia.com'],
            [
                'name' => 'Carlos Torres',
                'password' => Hash::make('carlos'),
            ]
        );

        // Crear Cuenta para Mateo
        $mateoUser = User::updateOrCreate(
            ['email' => 'mateo@barberia.com'],
            [
                'name' => 'Mateo Silva',
                'password' => Hash::make('mateo'),
            ]
        );

        $barberos = [
            [
                'name' => 'Carlos "Mano de Tijera" Torres',
                'role' => 'Barbero Principal / Estilista Clásico',
                'bio' => 'Con más de 8 años de trayectoria en barberías europeas, Carlos es experto en cortes clásicos ingleses y afeitado tradicional a navaja libre.',
                'image' => 'assets/barber_team_1.png',
                'user_id' => $carlosUser->id,
            ],
            [
                'name' => 'Mateo Silva',
                'role' => 'Especialista en Tendencias & Fades',
                'bio' => 'Mateo es el maestro de la barbería urbana. Experto en degradados extremos (Fades), peinados texturizados y diseños personalizados afeitados en cuero cabelludo.',
                'image' => 'assets/barber_team_2.png',
                'user_id' => $mateoUser->id,
            ]
        ];

        foreach ($barberos as $barb) {
            Specialist::updateOrCreate(
                ['name' => $barb['name']],
                $barb
            );
        }
    }
}
