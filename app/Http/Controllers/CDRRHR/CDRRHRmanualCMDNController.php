<?php

namespace App\Http\Controllers\CDRRHR;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class CDRRHRmanualCMDNController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CMDN_MANUAL');

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('CPR_NUMBER', 'like', "%{$search}%")
                  ->orWhere('PRODUCT_NAME', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query
                ->orderBy('DECISION_DATE', 'desc')
                ->paginate(20)
        );
    }

    public function add(Request $request)
    {
        DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CMDN_MANUAL')
            ->insert([
                'APP_UID' => $request->APP_UID,
                'CPR_NUMBER' => $request->CPR_NUMBER,
                'PRODUCT_NAME' => $request->PRODUCT_NAME,
                'COMPANY_NAME' => $request->COMPANY_NAME,
                'COMPANY_ADDRESS' => $request->COMPANY_ADDRESS,
                'DECISION_DATE' => $request->DECISION_DATE,
                'DATE_VALIDITY' => $request->DATE_VALIDITY,
                'LOW_RISK_DECISION' => $request->LOW_RISK_DECISION,
                'AUTHORIZATION_TYPE' => $request->AUTHORIZATION_TYPE,
                'IS_CANCELED' => $request->IS_CANCELED ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CMDN_MANUAL')
            ->where('CPR_NUMBER', $id)
            ->update([
                'APP_UID' => $request->APP_UID,
                'PRODUCT_NAME' => $request->PRODUCT_NAME,
                'COMPANY_NAME' => $request->COMPANY_NAME,
                'COMPANY_ADDRESS' => $request->COMPANY_ADDRESS,
                'DECISION_DATE' => $request->DECISION_DATE,
                'DATE_VALIDITY' => $request->DATE_VALIDITY,
                'LOW_RISK_DECISION' => $request->LOW_RISK_DECISION,
                'AUTHORIZATION_TYPE' => $request->AUTHORIZATION_TYPE,
                'IS_CANCELED' => $request->IS_CANCELED ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CMDN_MANUAL')
            ->where('CPR_NUMBER', $id)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($record);
    }

    /**
     * Handles file uploads, dynamically processing both CSV and XLSX formats
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file'
        ]);

        $file = $request->file('import_file');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());
        $rowsToUpsert = [];

        // 1. If it's a true Excel worksheet (.xlsx)
        if ($extension === 'xlsx' || $extension === 'xls') {
            if ($xlsx = SimpleXLSX::parse($filePath)) {
                $excelRows = $xlsx->rows();
                
                if (!empty($excelRows)) {
                    // Pull header columns out of row zero
                    $headers = array_map('trim', array_shift($excelRows));
                    
                    foreach ($excelRows as $rowData) {
                        if (count($headers) <= count($rowData)) {
                            $rowsToUpsert[] = array_combine(array_slice($headers, 0, count($rowData)), $rowData);
                        }
                    }
                }
            }
        } else {
            // 2. Fallback stream logic to support raw plain-text CSV format sheets
            if (($handle = fopen($filePath, 'r')) !== false) {
                $headers = array_map('trim', fgetcsv($handle, 1000, ','));
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($headers) === count($data)) {
                        $rowsToUpsert[] = array_combine($headers, $data);
                    }
                }
                fclose($handle);
            }
        }

        // 3. Execute "Upsert" behaviors (Update if existing, Insert if completely new)
        foreach ($rowsToUpsert as $row) {
            $CPRNumber = trim($row['CPR_NUMBER'] ?? '');

            if (empty($CPRNumber)) {
                continue;
            }

            DB::connection('cpr_cdrrhr')
                ->table('CDRRHR_CMDN_MANUAL')
                ->updateOrInsert(
                    ['CPR_NUMBER' => $CPRNumber], 
                    [
                        'APP_UID' => $row['APP_UID'] ?? null,
                        'PRODUCT_NAME' => $row['PRODUCT_NAME'] ?? null,
                        'COMPANY_NAME' => $row['COMPANY_NAME'] ?? null,
                        'COMPANY_ADDRESS' => $row['COMPANY_ADDRESS'] ?? null,
                        'DECISION_DATE' => $row['DECISION_DATE'] ?? null,
                        'DATE_VALIDITY' => $row['DATE_VALIDITY'] ?? null,
                        'LOW_RISK_DECISION' => $row['LOW_RISK_DECISION'] ?? null,
                        'AUTHORIZATION_TYPE' => $row['AUTHORIZATION_TYPE'] ?? null,
                        'IS_CANCELED' => $row['IS_CANCELED'] ?? 'N',
                    ]
                );
        }

        return response()->json([
            'success' => true,
            'message' => 'File records successfully imported/updated!'
        ]);
    }
}