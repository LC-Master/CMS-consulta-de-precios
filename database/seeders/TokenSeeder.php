<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all();

        if ($stores->isEmpty()) {
            $this->command->error("No se encontraron centros. Por favor, ejecuta primero el storeSeeder.");
            return;
        }

        $this->command->info("Generando tokens para " . $stores->count() . " centros...");
        foreach ($stores as $store) {
            $store->tokens()->delete();

            $tokenResult = $store->createToken("api-token-{$store->store_code}");

            $this->command->line("Centro: <info>{$store->name}</info> | Token: <comment>{$tokenResult->plainTextToken}</comment>");
        }

        $this->command->info("¡Proceso finalizado con éxito!");
    }
}
