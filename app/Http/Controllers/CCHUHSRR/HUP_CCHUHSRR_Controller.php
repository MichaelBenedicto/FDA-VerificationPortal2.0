<?php

namespace App\Http\Controllers\CCHUHSRR;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class HUP_CCHUHSRR_Controller extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('ccrr')
            ->table('CCHUHSRR_CPR_HUP');

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
        DB::connection('ccrr')
            ->table('CCHUHSRR_CPR_HUP')
            ->insert([
                'registration_number' => $request->registration_number,
                'product_name' => $request->product_name,
                'packaging' => $request->packaging,
                'active_ingredient' => $request->active_ingredient,
                'intended_use' => $request->intended_use,
                'manufacturer' => $request->manufacturer,
                'country_of_origin' => $request->country_of_origin,
                'distributor' => $request->distributor,
                'issuance_date' => $request->issuance_date,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'is_canceled' => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('ccrr')
            ->table('CCHUHSRR_CPR_HUP')
            ->where('registration_number', $id)
            ->update([
                'product_name' => $request->product_name,
                'packaging' => $request->packaging,
                'active_ingredient' => $request->active_ingredient,
                'intended_use' => $request->intended_use,
                'manufacturer' => $request->manufacturer,
                'country_of_origin' => $request->country_of_origin,
                'distributor' => $request->distributor,
                'issuance_date' => $request->issuance_date,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'is_canceled' => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('ccrr')
            ->table('CCHUHSRR_CPR_HUP')
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

            DB::connection('ccrr')
                ->table('CCHUHSRR_CPR_HUP')
                ->updateOrInsert(
                    ['registration_number' => $CPRNumber], 
                    [
                        'product_name' => $row['product_name'] ?? null,
                        'packaging' => $row['packaging'] ?? null,
                        'active_ingredient' => $row['active_ingredient'] ?? null,
                        'intended_use' => $row['intended_use'] ?? null,
                        'manufacturer' => $row['manufacturer'] ?? null,
                        'country_of_origin' => $row['country_of_origin'] ?? null,
                        'distributor' => $row['distributor'] ?? null,
                        'issuance_date' => $row['issuance_date'] ?? null,
                        'expiry_date' => $row['expiry_date'] ?? null,
                        'status' => $row['status'] ?? null,
                        'is_canceled' => $row['is_canceled'] ?? 'N',
                    ]
                );
        }

        return response()->json([
            'success' => true,
            'message' => 'File records successfully imported/updated!'
        ]);
    }
}