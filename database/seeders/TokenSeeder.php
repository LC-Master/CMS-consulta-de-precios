<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Center;
use Illuminate\Database\Seeder;
class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $centers = Center::all();

        if ($centers->isEmpty()) {
            $this->command->error("No se encontraron centros. Por favor, ejecuta primero el CenterSeeder.");
            return;
        }

        $this->command->info("Generando tokens para " . $centers->count() . " centros...");

        foreach ($centers as $center) {
            $center->tokens()->delete();

            $tokenResult = $center->createToken('api-token-' . $center->code);

            $this->command->line("Centro: <info>{$center->name}</info> | Token: <comment>{$tokenResult->plainTextToken}</comment>");
        }

        $this->command->info("¡Proceso finalizado con éxito!");
    }
}
