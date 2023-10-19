<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('clients')->insert([
            'name' => 'Ivan',
            'firstSurname' => 'Fernandez',
            'lastSurname' => 'Cardosa',
            'dni' => '21541875m',
            'adressCliente' => 'C/ pruebas Nº 5',
            'emailCliente' => 'test@example.com',
            'ciudadCliente' => 'Algeciras',
            'provinceCliente'=>'Cadiz',
            'postCodeCliente' => '11202',
            'tipoCliente' => 1
        ]);
    }
}
