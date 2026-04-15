<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceProductController extends Controller
{
    public function index()
{
    try {
        
        $products = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_MEDICAL_DEVICES')
                ->select(
                    'registration_number',
                    'product_name',
                    'manufacturer',
                    'country_of_origin',
                    'trader',
                    'distributor',
                    'issuance_date',
                    'expiry_date'
                )
                ->where('is_canceled', '=', 'N')
                ->whereRaw("expiry_date >= CURDATE()")
                ->get()
                ->map(function ($item) {
                    // FORMAT DATE
                    if (!empty($item->issuance_date)) {
                        try {
                            $item->issuance_date = Carbon::parse($item->issuance_date)->format('d F Y');
                        } catch (\Exception $e) {}
                    }
                    if (!empty($item->expiry_date)) {
                        try {
                            $item->expiry_date = Carbon::parse($item->expiry_date)->format('d F Y');
                        } catch (\Exception $e) {}
                    }
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