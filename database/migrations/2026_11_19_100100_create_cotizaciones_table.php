<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique()->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre_cliente', 80);
            $table->string('telefono', 20);
            $table->string('departamento', 40);
            $table->string('municipio', 60);
            $table->string('indicaciones', 500)->nullable();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('descuento');
            $table->unsignedBigInteger('transporte');
            $table->unsignedBigInteger('total');
            $table->string('estado', 20)->default('pendiente');
            $table->date('valida_hasta');
            $table->timestamps();
        });

        Schema::create('cotizacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            // Si el producto se elimina del panel, la cotización conserva el nombre y el precio.
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('nombre_producto', 150);
            $table->unsignedInteger('cantidad');
            $table->unsignedInteger('precio_unitario');
            $table->unsignedBigInteger('total_linea');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_items');
        Schema::dropIfExists('cotizaciones');
    }
};
