<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Thumbnail;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        Media::factory()
            ->count(50)
            ->has(Thumbnail::factory()) 
            ->create();
    }
}
