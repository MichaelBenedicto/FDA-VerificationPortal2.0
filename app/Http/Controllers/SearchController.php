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

            // Other LTO
             $otherEST_all = DB::connection('fdaeservices')
                ->select('CALL get_other_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $otherEST = array_filter($otherEST_all, function ($item) use ($q) {
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
           
             //CPR-CDRRHR       
            $cpr_cdrrhr = DB::connection('cpr_cdrrhr')
                ->table('medical_devices')
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
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
                        ->orWhere('product_name', 'LIKE', "%{$q}%");
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

                //healthcare_waste-CDRRHR       
            $healthcare_waste = DB::connection('cpr_cdrrhr')
                ->table('healthcare_waste')
                ->select(
                    'registration_number',
                    'product_name',
                    'company_name',
                    'intended_use_claim',
                    'issuance_date',
                    'expiry_date'
                )
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
                        ->orWhere('product_name', 'LIKE', "%{$q}%")
                        ->orWhere('company_name', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
               // ->whereRaw("expiry_date >= CURDATE()")
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
    
                //water_purification-CDRRHR       
            $water_purification = DB::connection('cpr_cdrrhr')
                ->table('water_purification_system')
                ->select(
                    'registration_number',
                    'product_name',
                    'company_name',
                    'intended_use_claim',
                    'issuance_date',
                    'expiry_date'
                )
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
                        ->orWhere('product_name', 'LIKE', "%{$q}%")
                        ->orWhere('company_name', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
               // ->whereRaw("expiry_date >= CURDATE()")
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
    
                 //xray-CDRRHR       
            $xray = DB::connection('cpr_cdrrhr')
                ->table('xray')
                ->select(
                    'license_number',
                    'name_of_establishment',
                    'owner',
                    'address',
                    'classification',
                    'issuance_date',
                    'expiry_date'
                )
                ->where(function ($query) use ($q) {
                    $query->where('license_number', 'LIKE', "%{$q}%")
                        ->orWhere('name_of_establishment', 'LIKE', "%{$q}%")
                        ->orWhere('owner', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
               // ->whereRaw("expiry_date >= CURDATE()")
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

                 //csl-batch       
            $csl_batch = DB::connection('csl')
                ->table('batch_notification')
                ->select(
                    'batch_notification_number',
                    'generic_name',
                    'brand_name',
                    'dosage_strength',
                    'dosage_form',
                    'registration_number',
                    'batch_number',
                    'lot_number',
                    'issuance_date',
                    'expiry_date'
                )
                ->where(function ($query) use ($q) {
                    $query->where('batch_notification_number', 'LIKE', "%{$q}%")
                        ->orWhere('generic_name', 'LIKE', "%{$q}%")
                        ->orWhere('brand_name', 'LIKE', "%{$q}%")
                        ->orWhere('registration_number', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();

                //csl-lot       
            $csl_lot = DB::connection('csl')
                ->table('lot_release_certificate')
                ->select(
                    'lot_release_number',
                    'generic_name',
                    'brand_name',
                    'dosage_strength',
                    'dosage_form',
                    'registration_number',
                    'batch_lot_number',
                    'packaging_lot',
                    'issuance_date',
                    'expiry_date'
                )
                ->where(function ($query) use ($q) {
                    $query->where('lot_release_number', 'LIKE', "%{$q}%")
                        ->orWhere('generic_name', 'LIKE', "%{$q}%")
                        ->orWhere('brand_name', 'LIKE', "%{$q}%")
                        ->orWhere('registration_number', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();

                //vat_exempt       
            $vat_exempt = DB::connection('vat_exempt')
                ->table('vat_exempt_prod_db')
                ->select(
                    'usage',
                    'generic_name',
                    'dosage_strength',
                    'dosage_form',
                    'date_publication'
                
                )
                ->where(function ($query) use ($q) {
                    $query->where('usage', 'LIKE', "%{$q}%")
                        ->orWhere('generic_name', 'LIKE', "%{$q}%")
                        ->orWhere('dosage_strength', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();

                //cosmetic-nn       
            $cosmetic_NN = DB::connection('PMT')
                ->table('COSMETIC_NOTIFICATION')
                ->select(
                    'ACCOUNTCODE',
                    'PRODUCT_NAME',
                    'BRAND_NAME',
                    'PROD_VARIANTS',
                    'COMPANY_NAME',
                    'NOTIFICATION_DECISION_DATE',
                    'NOTIFICATION_VALIDITY'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('BRAND_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //cmdn      
            $cmdn = DB::connection('PMT')
                ->table('Medical_Device_CMDN')
                ->select(
                    'CPR_NUMBER',
                    'PRODUCT_NAME',
                    'COMPANY_NAME',
                    'COMPANY_ADDRESS',
                    'AUTHORIZATION_TYPE',
                    'DECISION_DATE',
                    'DATE_VALIDITY'    
                )
                ->where(function ($query) use ($q) {
                    $query->where('CPR_NUMBER', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //Localcgmp      
            $localcgmp = DB::connection('GMP')
                ->table('LOCAL_GMP')
                ->select(
                    'CERT_NUM',
                    'COMPANY_NAME',
                    'CATEGORY',
                    'ADDRESS',
                    'VALIDITY_DATE',
                    'PRODUCTS'   
                )
                ->where(function ($query) use ($q) {
                    $query->where('CERT_NUM', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //Desktop Foreign cgmp      
            $desktopForeigncgmp = DB::connection('GMP')
                ->table('FOREIGN_GMP')
                ->select(
                    'CERT_NUM',
                    'MANUFACTURER_NAME',
                    'PLANT_ADDRESS',
                    'IMPORTER_NAME',
                    'VALIDITY_DATE',
                    'TYPE_OF_APPLICATION',
                    'PRODUCT_LINE'
                )
                ->where(function ($query) use ($q) {
                    $query->where('CERT_NUM', 'LIKE', "%{$q}%")
                        ->orWhere('MANUFACTURER_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //Inspected Foreign manuf      
            $inspectedForeign = DB::connection('GMP')
                ->table('INSPECTED_FOREIGN_MANUFACTURERS')
                ->select(
                    'CERT_NUM',
                    'MANUFACTURER_NAME',
                    'PLANT ADDRESS',
                    'IMPORTER_NAME',
                    'VALIDITY_DATE',
                    'TYPE_OF_APPLICATION',
                    'PRODUCT_LINE'
                )
                ->where(function ($query) use ($q) {
                    $query->where('CERT_NUM', 'LIKE', "%{$q}%")
                        ->orWhere('MANUFACTURER_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //Permit to Register    
            $PermitToRegister = DB::connection('GMP')
                ->table('PERMIT_TO_REGISTER')
                ->select(
                    'CERT_NUM',
                    'MANUFACTURER_NAME',
                    'PLANT_ADDRESS',
                    'IMPORTER_NAME',
                    'AUTHORIZED_PRODUCT_DOSAGE_FORM'
                )
                ->where(function ($query) use ($q) {
                    $query->where('CERT_NUM', 'LIKE', "%{$q}%")
                        ->orWhere('MANUFACTURER_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

            //lto_huhs       
            $lto_huhs = DB::connection('ccrr')
                ->table('lto_huhs')
                ->select(
                    'LTO_NO',
                    'ESTABLISHMENT_NAME',
                    'ESTABLISHMENT_OWNER',
                    'LTO_PREFERRED_ADDRESS_LABEL',
                    'PRIMARY_ACTIVITY_LABEL',
                    'SECONDARY_ACTVITY_STRING',
                    'PRODUCT_CLASSIFICATION_LABEL',
                    'DATE_DECISION_HUMAN',
                    'APPLICATION_TYPE',
                    'LTO_EXPIRY_HUMAN'  
                )
                ->where(function ($query) use ($q) {
                    $query->where('LTO_NO', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_OWNER', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();    


             // cpr_hup
            $cpr_hup = DB::connection('ccrr')
                ->table('hups')
                ->select(
                    'registration_number',
                    'product_name',
                    'active_ingredient',
                    'intended_use',
                    'manufacturer',
                    'country_of_origin',
                    'distributor',
                    'issuance_date',
                    'expiry_date',
                )
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
                        ->orWhere('product_name', 'LIKE', "%{$q}%")
                        ->orWhere('distributor', 'LIKE', "%{$q}%");
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

                //cpr_huhs       
            $cpr_huhs = DB::connection('ccrr')
                ->table('cpr_huhs')
                ->select(
                    'FERN_NO',
                    'PRODUCT_NAME',
                    'VARIANT_NAME',
                    'ACTIVE_INGREDIENTS',
                    'PRODUCT_CATEGORY_LABEL',
                    'PROD_SOURCE_EST_NAME',
                    'PROD_SOURCE_COUNTRY_LABEL',
                    'ESTABLISHMENT_NAME',
                    'DATE_DECISION_HUMAN',
                    'FERN_EXPIRY_HUMAN'   
                )
                ->where(function ($query) use ($q) {
                    $query->where('FERN_NO', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_NAME', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();    

                //tcca_notif      
            $tcca_notif = DB::connection('ccrr')
                ->table('PMT_TOYSMETA_38')
                ->select(
                    'ACCOUNTCODE',
                    'PRODUCT_BRAND_NAME',
                    'COMPANY_NAME',
                    'NOTIFICATION_VALIDITY'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_BRAND_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->where('NOTIFICATION_DECISION_LABEL', '=', 'Acknowledge')
                ->get();
                
                //Food gmp      
            $food_gmp = DB::connection('GMP')
                ->table('FOOD_GMP')
                ->select(
                    'ACCOUNTCODE',
                    'ESTABLISHMENT_NAME',
                    'ESTABLISHMENT_OWNER',
                    'PLANT_ADDRESS',
                    'LTO_ACTIVITY',
                    'ADDITIONAL_ACTIVITY',
                    'PRODUCTS',
                    'VALIDITY'   
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_OWNER', 'LIKE', "%{$q}%");
                })
                ->get();

                //HACCP Certificates  
            $HACCP = DB::connection('GMP')
                ->table('FOOD_HACCP')
                ->select(
                    'ACCOUNTCODE',
                    'ESTABLISHMENT_NAME',
                    'PLANT_ADDRESS'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('ESTABLISHMENT_NAME', 'LIKE', "%{$q}%");
                })
                ->get();

                //HACCP Products  
            $HACCPprod = DB::connection('GMP')
                ->table('FOOD_HACCP_PRODUCTS')
                ->select(
                    'ACCOUNTCODE',
                    'PRODUCT',
                    'VALIDITY_DATE'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT', 'LIKE', "%{$q}%");
                })
                ->get();

                // FDA Advisory
            $fdawebsite = DB::connection('fdawebsite')
                ->table('fda_advisories')
                ->select(
                    'date_posted',
                    'title',
                    'content',
                    'category',
                    'post_link'
                )
                ->where(function ($query) use ($q) {
                    $query->where('title', 'LIKE', "%{$q}%")
                        ->orWhere('content', 'LIKE', "%{$q}%");
                })
                ->get()
                ->map(function ($item) {
                    // FORMAT DATE
                    if (!empty($item->date_posted)) {
                        try {
                            $item->date_posted = Carbon::parse($item->date_posted)->format('d F Y');
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
                'cpr_cdrrhr' => $cpr_cdrrhr ?: [],
                'healthcare_waste' => $healthcare_waste ?: [],
                'water_purification' => $water_purification ?: [],
                'xray' => $xray ?: [],
                'csl_batch' => $csl_batch ?: [],
                'csl_lot' => $csl_lot ?: [],
                'vat_exempt' => $vat_exempt ?: [],
                'cosmetic_NN' => $cosmetic_NN ?: [],
                'cmdn' => $cmdn ?: [],
                'localcgmp' => $localcgmp ?: [],
                'desktopForeigncgmp' => $desktopForeigncgmp ?: [],
                'inspectedForeign' => $inspectedForeign ?: [],
                'PermitToRegister' => $PermitToRegister ?: [],
                'lto_huhs' => $lto_huhs ?: [],
                'cpr_hup' => $cpr_hup ?: [],
                'cpr_huhs' => $cpr_huhs ?: [],
                'tcca_notif' => $tcca_notif ?: [],
                'food_gmp' => $food_gmp ?: [],
                'HACCP' => $HACCP ?: [],
                'HACCPprod' => $HACCPprod ?: [],
                'otherEST' => $otherEST ?: [],
                'fdawebsite' => $fdawebsite ?: [],
        

            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
