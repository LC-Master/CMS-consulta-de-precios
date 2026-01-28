<?php
namespace App\Contracts;

interface LoggableDTO
{
    public function getId(): string;
    public function getType(): string;
    public function getProperties(): array;
}
