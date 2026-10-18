<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SCRUM-24 (CMS-06) - Hallazgo de seguridad 16-oct.
 *
 * Antes el rol por defecto era 'vendedor', así que cualquier visitante que
 * se registrara en el sitio público obtenía acceso al panel de productos.
 * Ahora el rol por defecto es 'cliente' (sin acceso al panel) y solo un
 * admin puede convertir a alguien en 'vendedor'.
 *
 * No se toca la migración original porque ya fue ejecutada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('cliente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('vendedor')->change();
        });
    }
};
