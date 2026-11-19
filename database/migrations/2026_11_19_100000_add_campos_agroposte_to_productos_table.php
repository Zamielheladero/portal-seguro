<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Diseño AgroPoste: datos que muestran el catálogo, los filtros y la ficha técnica.
 * Todos son opcionales para no romper los productos que ya existen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('categoria', 30)->nullable()->after('descripcion');
            $table->string('tratamiento', 30)->nullable()->after('categoria');
            $table->string('uso', 30)->nullable()->after('tratamiento');
            $table->string('material', 100)->nullable()->after('uso');
            $table->string('medida', 100)->nullable()->after('material');
            $table->decimal('largo_m', 4, 2)->nullable()->after('medida');
            $table->string('etiqueta', 40)->nullable()->after('largo_m');
            $table->unsignedTinyInteger('durabilidad_min')->nullable()->after('etiqueta');
            $table->unsignedTinyInteger('durabilidad_max')->nullable()->after('durabilidad_min');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'categoria', 'tratamiento', 'uso', 'material', 'medida',
                'largo_m', 'etiqueta', 'durabilidad_min', 'durabilidad_max',
            ]);
        });
    }
};
