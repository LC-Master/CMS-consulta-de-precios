<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThumbnailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'thumb_' . $this->faker->word() . '.jpg',
            'path' => 'thumbnails/' . $this->faker->uuid() . '.jpg',
            'mime_type' => 'image/jpeg',
            'size' => $this->faker->numberBetween(500, 2000),
        ];
    }
}
