<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugEstablishmentController extends Controller
{
    public function index(Request $request)
{
    set_time_limit(240); 
    ini_set('memory_limit', '512M'); // Prevents memory exhaustion during JSON encoding
    try {
        
        $searchQuery = $request->query('search', '');

        $establishments = DB::connection('lto_drugs')
            ->select('CALL get_drug_est_verif_by_search(?)', [$searchQuery]);

        
        $data = collect($establishments);

        
        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false, 
            'message' => 'Database error or timeout: ' . $e->getMessage()
        ], 500);
    }
}
}