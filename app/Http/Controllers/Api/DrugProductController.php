<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DrugProductController extends Controller
{
    public function index()
{
    try {
        
        $products = DB::connection('cdrr')
            ->table('all_drugproducts')
            ->where('is_canceled', 'N')
            ->whereRaw("expiry_date >= CURDATE()")
            ->get(); 

        
        $formattedProducts = $products->map(function ($item) {
            $item->issuance_date = $item->issuance_date 
                ? \Carbon\Carbon::parse($item->issuance_date)->format('d F Y') 
                : null;
                
            $item->expiry_date = $item->expiry_date 
                ? \Carbon\Carbon::parse($item->expiry_date)->format('d F Y') 
                : null;
                
            return $item;
        });

        return response()->json([
            'success' => true, 
            'count' => $products->count(),
            'data' => $formattedProducts
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => $e->getMessage()
        ], 500);
    }
}
}