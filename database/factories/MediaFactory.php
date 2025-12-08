<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    public function definition(): array
    {
        // Decidimos aleatoriamente si es video o imagen para variar la data
        $isVideo = $this->faker->boolean(30); // 30% probabilidad de ser video
        $mimeType = $isVideo ? 'video/mp4' : 'image/jpeg';
        
        return [
            'disk' => 'public',
            'path' => 'uploads/' . $this->faker->uuid() . ($isVideo ? '.mp4' : '.jpg'),
            'mime_type' => $mimeType,
            'size' => $this->faker->numberBetween(1024, 50000000), // Entre 1KB y 50MB
            'duration_seconds' => $isVideo ? $this->faker->numberBetween(10, 300) : null,
            'checksum' => md5($this->faker->text()),
            // Asumimos que el usuario ID 1 existe (creado en el seeder o manualmente)
            'created_by' => 1, 
        ];
    }
}
