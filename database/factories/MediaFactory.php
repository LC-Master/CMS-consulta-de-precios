<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    public function definition(): array
    {
        $isVideo = $this->faker->boolean(30); 
        $mimeType = $isVideo ? 'video/mp4' : 'image/jpeg';
        
        return [
            'disk' => 'public',
            'path' => 'uploads/' . $this->faker->uuid() . ($isVideo ? '.mp4' : '.jpg'),
            'mime_type' => $mimeType,
            'size' => $this->faker->numberBetween(1024, 50000000),
            'duration_seconds' => $isVideo ? $this->faker->numberBetween(10, 300) : null,
            'checksum' => md5($this->faker->text()),
            'created_by' => 1, 
        ];
    }
}
