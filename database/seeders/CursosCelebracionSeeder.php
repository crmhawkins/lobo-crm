<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CursosCelebracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cursos_celebracion')->insert([
            ['nombre' => 'Madrid'],
            ['nombre' => 'Hospitalet de Llobregat (Barcelona)'],
            ['nombre' => 'Pamplona'],
            ['nombre' => 'Bilbao'],
            ['nombre' => 'Línea de la Concepción (Cádiz)'],
            ['nombre' => 'Granada'],
            ['nombre' => 'Redondela (Vigo)'],
            ['nombre' => 'Valladolid'],
            ['nombre' => 'Valencia'],
            ['nombre' => 'Las Palmas de G.C.'],
            ['nombre' => 'Lugo'],
            ['nombre' => 'Murcia'],

        ]);
    }
}
