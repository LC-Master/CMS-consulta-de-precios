<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentsModel extends Model
{
    protected $table = "departments";

    protected $primaryKey = "id";

    public $timestamps = true;
}
