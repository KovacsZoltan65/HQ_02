<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponnetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('componnets')->truncate();

        $arr_components = [
            ['id' => 1,'name' => 'Központi komponens',],
            ['id' => 2,'name' => 'Központi komponens',],
        ];
        $count = count($arr_components);

        $this->command->warn(PHP_EOL . 'Creating Componnet...');

        $this->command->getOutput()->progressStart($count);
        foreach( $arr_components as $component )
        {
            \App\Models\Component::factory()->create($component);
            $this->command->getOutput()->progressAdvance();
        }
        $this->command->getOutput()->progressFinish();

        $this->command->info(PHP_EOL . 'Componnets created');
    }
}
