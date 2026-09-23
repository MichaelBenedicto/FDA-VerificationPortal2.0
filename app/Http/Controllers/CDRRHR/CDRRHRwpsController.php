<?php

namespace App\Http\Controllers\CDRRHR;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class CDRRHRwpsController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_WPS');

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query
                ->orderBy('issuance_date', 'desc')
                ->paginate(20)
        );
    }

    public function add(Request $request)
    {
        DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_WPS')
            ->insert([
                'registration_number' => $request->registration_number,
                'product_name'              => $request->product_name,
                'company_name'                => $request->company_name,
                'intended_use_claim'           => $request->intended_use_claim,
                'issuance_date'              => $request->issuance_date,
                'expiry_date'                => $request->expiry_date,
                'is_canceled'               => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_WPS')
            ->where('registration_number', $id)
            ->update([
                'product_name'              => $request->product_name,
                'company_name'                => $request->company_name,
                'intended_use_claim'           => $request->intended_use_claim,
                'issuance_date'              => $request->issuance_date,
                'expiry_date'                => $request->expiry_date,
                'is_canceled'               => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_WPS')
            ->where('registration_number', $id)
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
            $CPRNumber = trim($row['registration_number'] ?? '');

            if (empty($CPRNumber)) {
                continue;
            }

            DB::connection('cpr_cdrrhr')
                ->table('CDRRHR_CPR_MEDICAL_DEVICES')
                ->updateOrInsert(
                    ['registration_number' => $CPRNumber], 
                    [
                        'product_name'        => $row['product_name'] ?? null,
                        'company_name'          => $row['company_name'] ?? null,
                        'intended_use_claim'     => $row['intended_use_claim'] ?? null,
                        'issuance_date'        => $row['issuance_date'] ?? null,
                        'expiry_date'          => $row['expiry_date'] ?? null,
                        'is_canceled'         => $row['is_canceled'] ?? 'N',
                    ]
                );
        }

        return response()->json([
            'success' => true,
            'message' => 'File records successfully imported/updated!'
        ]);
    }
}