<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

/**
 * Catálogo inicial del diseño AgroPoste. Se puede correr varias veces:
 * actualiza por nombre en vez de duplicar.
 */
class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                'nombre' => 'Estacón de eucalipto',
                'descripcion' => 'Ideal para cercas ganaderas de larga duración.',
                'categoria' => 'estacones', 'tratamiento' => 'inmunizado', 'uso' => 'cerca',
                'material' => 'Eucalipto inmunizado', 'medida' => '2,2 m × 10–12 cm', 'largo_m' => 2.2,
                'etiqueta' => 'Más vendido', 'durabilidad_min' => 12, 'durabilidad_max' => 15,
                'precio' => 22900, 'stock' => 800,
            ],
            [
                'nombre' => 'Poste de guayacán',
                'descripcion' => 'Madera dura para esquinas, puertas y corrales que reciben carga.',
                'categoria' => 'estacones', 'tratamiento' => 'natural', 'uso' => 'corral',
                'material' => 'Guayacán natural', 'medida' => '2,5 m × 12–14 cm', 'largo_m' => 2.5,
                'etiqueta' => 'Alta duración', 'durabilidad_min' => 18, 'durabilidad_max' => 20,
                'precio' => 38900, 'stock' => 200,
            ],
            [
                'nombre' => 'Vareta de pino',
                'descripcion' => 'Liviana y económica para divisiones y cercas internas.',
                'categoria' => 'varetas', 'tratamiento' => 'inmunizado', 'uso' => 'cerca',
                'material' => 'Pino inmunizado', 'medida' => '3 m × 7–9 cm', 'largo_m' => 3.0,
                'etiqueta' => 'Inmunizado', 'durabilidad_min' => 8, 'durabilidad_max' => 10,
                'precio' => 16800, 'stock' => 600,
            ],
            [
                'nombre' => 'Tabla para corral',
                'descripcion' => 'Tabla cepillada para corrales, bebederos y mangas.',
                'categoria' => 'tablas', 'tratamiento' => 'inmunizado', 'uso' => 'corral',
                'material' => 'Eucalipto cepillado', 'medida' => '3 m × 20 cm', 'largo_m' => 3.0,
                'etiqueta' => 'Certificado', 'durabilidad_min' => 10, 'durabilidad_max' => 12,
                'precio' => 46500, 'stock' => 300,
            ],
            [
                'nombre' => 'Alambre de púas',
                'descripcion' => 'Rollo galvanizado calibre 12,5 para cercas de potrero.',
                'categoria' => 'alambre', 'tratamiento' => 'galvanizado', 'uso' => 'cerca',
                'material' => 'Calibre 12,5', 'medida' => 'Rollo de 400 m', 'largo_m' => null,
                'etiqueta' => 'Más vendido', 'durabilidad_min' => 6, 'durabilidad_max' => 8,
                'precio' => 174900, 'stock' => 120,
            ],
            [
                'nombre' => 'Grapa galvanizada',
                'descripcion' => 'Grapas para fijar el alambre a postes y estacones.',
                'categoria' => 'alambre', 'tratamiento' => 'galvanizado', 'uso' => 'construccion',
                'material' => 'Acero galvanizado', 'medida' => 'Caja de 5 kg', 'largo_m' => null,
                'etiqueta' => 'Complemento', 'durabilidad_min' => 8, 'durabilidad_max' => 10,
                'precio' => 54900, 'stock' => 150,
            ],
        ];

        foreach ($productos as $producto) {
            Producto::updateOrCreate(['nombre' => $producto['nombre']], $producto + ['activo' => true]);
        }
    }
}
