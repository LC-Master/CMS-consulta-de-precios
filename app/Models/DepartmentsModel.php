<?php

namespace App\Models;

use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(DepartmentFactory::class)]
class DepartmentsModel extends Model
{
    use HasFactory;
    protected $table = "departments";

    protected $primaryKey = "id";

    public $timestamps = true;
}
