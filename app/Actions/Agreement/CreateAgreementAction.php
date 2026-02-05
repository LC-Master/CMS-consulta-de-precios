<?php

namespace App\Actions\Agreement;

use App\Enums\AgreementStatus;
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

            $data['is_active'] = 1;

            $existingAgreement = Agreement::withTrashed()
                ->where(function ($query) use ($data) {
                    $query->where('name', $data['name'])
                          ->orWhere('tax_id', $data['tax_id']);
                })
                ->first();

            if ($existingAgreement) {
                
                if ($existingAgreement->trashed()) {
                    $existingAgreement->restore();
                }

                $existingAgreement->update($data);

                return $existingAgreement;
            }

            return Agreement::create($data);
        });
    }
}