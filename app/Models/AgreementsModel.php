<?php

namespace App\Models;

use Database\Factories\AgreementFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[UseFactory(AgreementFactory::class)]
class AgreementsModel extends Model
{
    use HasFactory;
    protected $table = "agreements";

    protected $primaryKey = "id";

    public $timestamps = true;

}
