<?php

namespace App\Enums;

enum MimeTypesEnum: string
{
    case ImageJpeg = 'image/jpeg';
    case ImagePng = 'image/png';
    case VideoMp4 = 'video/mp4';
    case ImageWebp = 'image/webp';


    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }
}
