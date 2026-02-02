<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $suppliers = Supplier::select([
                'id', 
                'SupplierName', 
                'AccountNumber', // RIF
                'ContactName', 
                'EmailAddress', 
                'PhoneNumber', 
                'Notes'
            ])
            ->where('SupplierName', 'LIKE', "%{$query}%")
            ->orWhere('AccountNumber', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get();

        $formatted = $suppliers->map(function ($s) {
            return [
                'value' => $s->id,
                'label' => "{$s->SupplierName} - {$s->AccountNumber}",
                'original' => $s
            ];
        });

        return response()->json($formatted);
    }
}