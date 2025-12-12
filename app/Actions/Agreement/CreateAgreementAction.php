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

            // Verificamos si 'Activo' NO ESTÁ y si 'Inactivo' TAMBIÉN NO ESTÁ
            if (!in_array('Activo', $valoresValidos) && !in_array('Inactivo', $valoresValidos)) {
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
