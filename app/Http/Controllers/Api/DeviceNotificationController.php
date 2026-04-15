<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // 1. Added this import
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceNotificationController extends Controller
{
    // 2. Added (Request $request) here
    public function index(Request $request)
    {
        try {
            $searchQuery = $request->query('search', '');

            $notifications = DB::connection('cpr_cdrrhr')
                ->select('CALL get_cdrrhr_cmdn_verif_by_search(?)', [$searchQuery]);

            // 3. Cast to collection
            $data = collect($notifications);

            return response()->json([
                'success' => true, 
                // 4. Use $data->count() instead of $products->count()
                'count' => $data->count(),
                // 5. Use $data instead of $formattedProducts
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}