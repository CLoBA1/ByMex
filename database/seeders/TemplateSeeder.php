<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tour;
use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TemplateSeeder extends Seeder
{
    /**
     * Seed de datos iniciales para una instalación limpia de la plantilla.
     *
     * Uso: php artisan db:seed --class=TemplateSeeder
     *
     * NO incluye datos reales de ningún negocio.
     * NO incluye credenciales reales.
     * NO incluye teléfonos, correos ni tokens de producción.
     */
    public function run(): void
    {
        // 1. Crear usuario administrador demo
        if (User::count() === 0) {
            User::create([
                'name'     => 'Administrador',
                'email'    => 'admin@demo.com',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ]);
            $this->command->info('Usuario admin creado: admin@demo.com / password');
        } else {
            $this->command->warn('Ya existe al menos un usuario admin. Saltando.');
        }

        // 2. Crear configuración base de pagos/políticas
        if (PaymentSetting::count() === 0) {
            PaymentSetting::create([
                'business_name'         => 'Mi Negocio',
                'whatsapp_number'       => '5500000000',
                'general_instructions'  => 'Realiza tu depósito o transferencia a cualquiera de las cuentas listadas y envía tu comprobante por WhatsApp.',
                'reservation_policies'  => 'Al realizar tu reservación aceptas los términos y condiciones del servicio.',
                'cancellation_policies' => 'Las cancelaciones están sujetas a penalización según la fecha de aviso.',
                'final_note'            => 'Gracias por tu preferencia.',
            ]);
            $this->command->info('Configuración base de pagos creada.');
        } else {
            $this->command->warn('Ya existe configuración de pagos. Saltando.');
        }

        // 3. Crear un servicio/tour demo (opcional)
        if (Tour::count() === 0) {
            Tour::create([
                'title'            => 'Servicio Demo',
                'destination'      => 'Destino de Ejemplo',
                'departure_date'   => now()->addDays(30),
                'price'            => 1500.00,
                'minimum_deposit'  => 500.00,
                'total_seats'      => 40,
                'expiration_hours' => 48,
                'status'           => 'active',
                'description'      => 'Este es un servicio de demostración. Edítalo o elimínalo desde el panel de administración.',
            ]);
            $this->command->info('Servicio demo creado.');
        } else {
            $this->command->warn('Ya existen servicios. Saltando.');
        }

        $this->command->info('TemplateSeeder completado.');
    }
}
