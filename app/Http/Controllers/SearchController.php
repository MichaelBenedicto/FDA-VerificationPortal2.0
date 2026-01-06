<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q');

        if (!$q) {
            return response()->json([
                'error' => 'Missing search parameter.'
            ], 400);
        }

        try {
            // FOOD LTO
            $lto_food = DB::connection('lto_food')
                ->select('CALL get_food_est_verif_by_search(?)', [$q]);

            // DRUG LTO
            $lto_drugs = DB::connection('lto_drugs')
                ->select('CALL get_drug_est_verif_by_search(?)', [$q]);

            // DEVICE LTO
            $lto_medicaldevice = DB::connection('lto_medicaldevice')
                ->select('CALL get_device_est_verif_by_search(?)', [$q]);

            // HEALTH RELATED LTO
            $lto_healthrelateddevice = DB::connection('lto_healthrelateddevice')
                ->select('CALL get_healthrelateddevice_est_verif_by_search(?)', [$q]);

            // PCO LTO
            $lto_pco = DB::connection('lto_pco')
                ->select('CALL get_pco_verif_by_search(?)', [$q]);

            // COSMETIC LTO
             $lto_cosmetics_all = DB::connection('lto_cosmetics')
                ->select('CALL get_cosmetic_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_cosmetics = array_filter($lto_cosmetics_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // HUP LTO
             $lto_hup_all = DB::connection('lto_hup')
                ->select('CALL get_hup_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_hup = array_filter($lto_hup_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // TCCA LTO
             $lto_tcca_all = DB::connection('lto_tcca')
                ->select('CALL get_tcca_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_tcca = array_filter($lto_tcca_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // FOOD CPR
            $fdafoodproducts = DB::connection('fdafoodproducts')
                ->table('food_products')
                ->select(
                    'ACCOUNTCODE',
                    'PRODUCT_NAME',
                    'BRAND_NAME',
                    'COMPANY_NAME',
                    'DECISION_DATE',
                    'DATE_VALIDITY'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('BRAND_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->where('LOW_RISK_DECISION', '=', 'Approved')
                ->where('IS_CANCELED', '=', 'N')
                ->where('DATE_VALIDITY', '!=', 'Not Applicable')
                ->whereRaw("STR_TO_DATE(DATE_VALIDITY, '%d %M %Y') >= CURDATE()")
                ->get()
                ->map(function ($item) {
                    // ✅ Format FDA food product dates
                    if (!empty($item->DECISION_DATE) && $item->DECISION_DATE !== 'Not Applicable') {
                        try {
                            $item->DECISION_DATE = Carbon::parse($item->DECISION_DATE)->format('d F Y');
                        } catch (\Exception $e) {}
                    }
                    if (!empty($item->DATE_VALIDITY) && $item->DATE_VALIDITY !== 'Not Applicable') {
                        try {
                            $item->DATE_VALIDITY = Carbon::parse($item->DATE_VALIDITY)->format('d F Y');
                        } catch (\Exception $e) {}
                    }
                    return $item;
                });

            // DRUG CPR
            $cdrr = DB::connection('cdrr')
                ->table('all_drugproducts')
                ->select(
                    'registration_number',
                    'generic_name',
                    'brand_name',
                    'dosage_strength',
                    'dosage_form',
                    'classification',
                    'packaging',
                    'manufacturer',
                    'country_of_origin',
                    'trader',
                    'importer',
                    'distributor',
                    'app_type',
                    'issuance_date',
                    'expiry_date',
                    'pharmacologic_category'
                )
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
                        ->orWhere('generic_name', 'LIKE', "%{$q}%")
                        ->orWhere('brand_name', 'LIKE', "%{$q}%");
                })
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
           

            // REPONSE
            $data = [
                'lto_food' => $lto_food ?: [],
                'lto_drugs' => $lto_drugs ?: [],
                'lto_medicaldevice' => $lto_medicaldevice ?: [],
                'lto_healthrelateddevice' => $lto_healthrelateddevice ?: [],
                'lto_pco' => $lto_pco ?: [],
                'fdafoodproducts' => $fdafoodproducts ?: [],
                'cdrr' => $cdrr ?: [],
                'lto_cosmetics' => array_values($lto_cosmetics),
                'lto_hup' => array_values($lto_hup),
                'lto_tcca' => array_values($lto_tcca),

            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
