<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);

        $q = Supplier::select([
                'id', 
                'SupplierName', 
                'AccountNumber', // RIF
                'ContactName', 
                'EmailAddress', 
                'PhoneNumber', 
                'Notes'
            ]);

        if (!empty($query) && strlen($query) >= 2) {
            $q->where(function($w) use ($query) {
                $w->where('SupplierName', 'LIKE', "%{$query}%")
                  ->orWhere('AccountNumber', 'LIKE', "%{$query}%");
            });
        }

        $suppliers = $q->paginate(10, ['*'], 'page', $page);

        $formatted = collect($suppliers->items())->map(function ($s) {
            return [
                'value' => $s->id,
                'label' => "{$s->SupplierName} - {$s->AccountNumber}",
                'original' => $s
            ];
        });

        return response()->json([
            'options' => $formatted,
            'hasMore' => $suppliers->hasMorePages()
        ]);
    }
}