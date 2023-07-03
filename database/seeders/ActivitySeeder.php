<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Activity::create([
            'user_id' => 1,
            'title' => 'Cultura y Capilla',
            'body' => 'Programa de cultura y capilla para estudiantes de Ingenieria de sistemas',
            'date' => '23-04-07'
        ]);

        Activity::create([
            'user_id' => 1,
            'title' => 'Jornada Cientifica',
            'body' => 'Mas tarea para uds',
            'date' => '23-05-17'
        ]);

        Activity::create([
            'user_id' => 1,
            'title' => 'Semana de Enfasis Espiritual',
            'body' => 'Preparemonos para nuestra primera semana de oraciòn',
            'date' => '23-06-11'
        ]);
    }
}
