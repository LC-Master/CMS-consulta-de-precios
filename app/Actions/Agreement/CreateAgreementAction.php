<?php

namespace App\Actions\Agreement;
use App\Enums\AgreementStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\Agreement;

class CreateAgreementAction
{
     public function execute(array $data): Agreement
    {
        return DB::transaction(function () use ($data) {

            $valoresValidos = array_column(AgreementStatus::cases(), 'value');

            if (!\in_array(AgreementStatus::ACTIVE->value, $valoresValidos) && !\in_array(AgreementStatus::INACTIVE->value, $valoresValidos)) {
                Log::critical('Integrity Error: Default agreement status missing.', ['required_status' => AgreementStatus::ACTIVE->value]);
                throw new \RuntimeException('Error de configuración del sistema: Estado inicial no encontrado.');
            }

            $agreement = Agreement::create(array_merge($data, [
                'is_active' => 1,
            ]));

            return $agreement;
        });
    }
}
