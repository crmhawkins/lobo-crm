<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CursosDenominacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cursos_denominacion')->insert([
            ['nombre' => 'Curso Nivel Of.-Basic.'],
            ['nombre' => 'Reciclaje Nivel Of.-Basic.'],
            ['nombre' => 'Examen Nivel Of.-Basic.']
        ]);
    }
}
