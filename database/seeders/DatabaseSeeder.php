<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // SCRUM-24: el registro público ahora crea usuarios 'cliente', así que
        // el primer administrador se crea aquí. Las credenciales salen del .env
        // (ADMIN_EMAIL / ADMIN_PASSWORD) para no dejar contraseñas en el código.
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD no están definidos en .env: no se creó el administrador.');

            return;
        }

        $admin = User::firstOrNew(['email' => strtolower($email)]);
        $admin->name = $admin->name ?: 'Administrador';
        $admin->password = $password; // el cast 'hashed' del modelo lo cifra con bcrypt
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        $this->command?->info("Administrador listo: {$admin->email}");
    }
}
