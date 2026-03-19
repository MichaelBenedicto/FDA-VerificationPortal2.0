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
            // FOOD LTO - cds
            $lto_food = DB::connection('lto_food')
                ->select('CALL get_food_est_verif_by_search(?)', [$q]);

            // DRUG LTO - cds
            $lto_drugs = DB::connection('lto_drugs')
                ->select('CALL get_drug_est_verif_by_search(?)', [$q]);

            // DEVICE LTO - cds
            $lto_medicaldevice = DB::connection('lto_medicaldevice')
                ->select('CALL get_device_est_verif_by_search(?)', [$q]);

            // HEALTH RELATED LTO - cds
            $lto_healthrelateddevice = DB::connection('lto_healthrelateddevice')
                ->select('CALL get_healthrelateddevice_est_verif_by_search(?)', [$q]);

            // PCO LTO - cds
            $lto_pco = DB::connection('lto_pco')
                ->select('CALL get_pco_verif_by_search(?)', [$q]);

            // HUHS LTO - cds
            $lto_huhs = DB::connection('lto_huhs')
                ->select('CALL get_huhs_est_verif_by_search(?)', [$q]);

            // COSMETIC LTO - old verif
             $lto_cosmetics_all = DB::connection('lto_cosmetics')
                ->select('CALL get_cosmetic_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_cosmetics = array_filter($lto_cosmetics_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // HUP LTO - old verif
             $lto_hup_all = DB::connection('lto_hup')
                ->select('CALL get_hup_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_hup = array_filter($lto_hup_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // TCCA LTO - old verif
             $lto_tcca_all = DB::connection('lto_tcca')
                ->select('CALL get_tcca_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $lto_tcca = array_filter($lto_tcca_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });

            // Other LTO - old verif
             $otherEST_all = DB::connection('fdaeservices')
                ->select('CALL get_other_est_verif_by_search()');

            // Filter by search query (case-insensitive)
            $otherEST = array_filter($otherEST_all, function ($item) use ($q) {
                return stripos($item->LTO_NUMBER, $q) !== false
                    || stripos($item->ESTABLISHMENT_NAME, $q) !== false
                    || stripos($item->ESTABLISHMENT_OWNER, $q) !== false
                    || stripos($item->LTO_ACTIVITY_LABEL, $q) !== false;
            });


            // FOOD CPR - new verif
            $fdafoodproducts = DB::connection('fdafoodproducts')
            ->select('CALL get_food_cpr_verif_by_search(?)', [$q]);

            // DRUG CPR - old verif
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
           
             //CPR-CDRRHR  - new verif     
            $cpr_cdrrhr = DB::connection('cpr_cdrrhr')
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

                //healthcare_waste-CDRRHR - new verif    
            $healthcare_waste = DB::connection('cpr_cdrrhr')
                ->table('CDRRHR_CPR_HCW')
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
    
                //water_purification-CDRRHR - new verif    
            $water_purification = DB::connection('cpr_cdrrhr')
                ->table('CDRRHR_CPR_WPS')
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
    
                 //xray-CDRRHR - new verif      
            $xray = DB::connection('cpr_cdrrhr')
                ->table('CDRRHR_XRAY')
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

                 //csl-batch - new verif
            $csl_batch = DB::connection('csl')
                ->table('CSL_BATCH_NOTIF')
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

                //csl-lot - new verif    
            $csl_lot = DB::connection('csl')
                ->table('CSL_LOT_RELEASE')
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

                //vat_exempt - new verif       
            $vat_exempt = DB::connection('cdrr_new')
                ->table('VAT_EXEMPT')
                ->select(
                    'usage',
                    'generic_name',
                    'dosage_strength',
                    'dosage_form',
                    'date_publication',
                    'category'
                
                )
                ->where(function ($query) use ($q) {
                    $query->where('usage', 'LIKE', "%{$q}%")
                        ->orWhere('generic_name', 'LIKE', "%{$q}%")
                        ->orWhere('dosage_strength', 'LIKE', "%{$q}%")
                        ->orWhere('category', 'LIKE', "%{$q}%");
                })
                ->where('is_canceled', '=', 'N')
                ->get();

                //PI/PIL
                $cdrr_PIPIL = DB::connection('cdrr_new')
                ->table('PI_PIL')
                ->select(
                    'file_link',
                    'version',
                    'registration_number',
                    'generic_name',
                    'brand_name',
                    'category'
                
                )
                ->where(function ($query) use ($q) {
                    $query->where('registration_number', 'LIKE', "%{$q}%")
            ->orWhere('generic_name', 'LIKE', "%{$q}%")
            ->orWhere('brand_name', 'LIKE', "%{$q}%")
            ->orWhere('category', 'LIKE', "%{$q}%");
                })

                ->get();

                //cosmetic-nn - new verif     
            $cosmetic_NN = DB::connection('ccrr')
            ->select('CALL get_cosmetic_notif_verif_by_search(?)', [$q]);

                //cmdn -new verif
            $cmdn = DB::connection('cpr_cdrrhr')
        ->select('CALL get_cdrrhr_cmdn_verif_by_search(?)',
        [$q]
    );


                //Localcgmp - new verif      
            $localcgmp = DB::connection('cdrr_new')
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

                //Desktop Foreign cgmp - new verif      
            $desktopForeigncgmp = DB::connection('cdrr_new')
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

                //Inspected Foreign manuf - new verif    
            $inspectedForeign = DB::connection('cdrr_new')
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

                //Permit to Register - new verif     
            $PermitToRegister = DB::connection('cdrr_new')
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


             // cpr_hup - new verif 
            $cpr_hup = DB::connection('ccrr')
                ->table('CCHUHSRR_CPR_HUP')
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

                //cpr_huhs - old verif       
            $cpr_huhs = DB::connection('ccrr_old')
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

                //tcca_notif  - new verif     
            $tcca_notif = DB::connection('eportalverif2')
                ->table('PMT_CCHUHSRR_TCCA_NOTIFICATION')
                ->select(
                    'ACCOUNTCODE',
                    'APP_NUMBER',
                    'PRODUCT_BRAND_NAME',
                    'COMPANY_NAME',
                    'NOTIFICATION_VALIDITY',
                    'APP_NUMBER'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ACCOUNTCODE', 'LIKE', "%{$q}%")
                        ->orWhere('PRODUCT_BRAND_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('COMPANY_NAME', 'LIKE', "%{$q}%");
                })
                ->where('NOTIFICATION_DECISION_LABEL', '=', 'Acknowledge')
                ->get();

                //tcca_notif_products  - new verif     
            $tcca_notif_products = DB::connection('eportalverif2')
                ->table('PMT_PMT_CCHUHSRR_TCCA_NOTIFICATION_PRODUCT_DETAILS')
                ->select(
                    'ITEM_NAME',
                    'ITEM_MODEL_NO',
                    'ROW',
                    'ITEM_SKU',
                    'ITEM_AGE_GRADING_LABEL',
                    'APP_STATUS',
                    'APP_NUMBER'
                )
                ->where(function ($query) use ($q) {
                    $query->where('ITEM_NAME', 'LIKE', "%{$q}%")
                        ->orWhere('ITEM_MODEL_NO', 'LIKE', "%{$q}%")
                        ->orWhere('ITEM_SKU', 'LIKE', "%{$q}%");
                })
                ->whereIn('APP_STATUS', ['COMPLETED', 'TO_DO'])
                ->get();
                
                //Food gmp - new verif    
            $food_gmp = DB::connection('fdafoodproducts')
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

                //HACCP Certificates  - new verif
            $HACCP = DB::connection('fdafoodproducts')
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

                //HACCP Products  - new verif
            $HACCPprod = DB::connection('fdafoodproducts')
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

                // FDA Advisory - website
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
                'tcca_notif_products' => $tcca_notif_products ?: [],
                'cdrr_PIPIL' => $cdrr_PIPIL ?: [],
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Query failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
