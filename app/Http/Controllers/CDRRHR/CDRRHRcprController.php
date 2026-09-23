<?php

namespace App\Http\Controllers\CDRRHR;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class CDRRHRcprController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_MEDICAL_DEVICES');

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
            ->table('CDRRHR_CPR_MEDICAL_DEVICES')
            ->insert([
                'registration_number' => $request->registration_number,
                'product_name'        => $request->product_name,
                'manufacturer'        => $request->manufacturer,
                'country_of_origin'   => $request->country_of_origin,
                'trader'              => $request->trader,
                'distributor'         => $request->distributor,
                'issuance_date'       => $request->issuance_date,
                'expiry_date'         => $request->expiry_date,
                'is_canceled'         => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_MEDICAL_DEVICES')
            ->where('registration_number', $id)
            ->update([
                'product_name'      => $request->product_name,
                'manufacturer'      => $request->manufacturer,
                'country_of_origin' => $request->country_of_origin,
                'trader'            => $request->trader,
                'distributor'       => $request->distributor,
                'issuance_date'     => $request->issuance_date,
                'expiry_date'       => $request->expiry_date,
                'is_canceled'       => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('cpr_cdrrhr')
            ->table('CDRRHR_CPR_MEDICAL_DEVICES')
            ->where('registration_number', $id)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json($record);
    }

    /**
     * Handles file uploads, dynamically processing both CSV and XLSX formats in bulk
     */
    public function import(Request $request)
    {
        // 1. Remove max execution time limit for long-running imports
        set_time_limit(0);

        $request->validate([
            'import_file' => 'required|file'
        ]);

        $file = $request->file('import_file');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());
        $recordsToInsert = [];

        // 2. Extract raw data from XLSX or CSV
        if ($extension === 'xlsx' || $extension === 'xls') {
            if ($xlsx = SimpleXLSX::parse($filePath)) {
                $excelRows = $xlsx->rows();
                
                if (!empty($excelRows)) {
                    $headers = array_map('trim', array_shift($excelRows));
                    
                    foreach ($excelRows as $rowData) {
                        if (count($headers) <= count($rowData)) {
                            $row = array_combine(array_slice($headers, 0, count($rowData)), $rowData);
                            $this->prepareRow($row, $recordsToInsert);
                        }
                    }
                }
            }
        } else {
            if (($handle = fopen($filePath, 'r')) !== false) {
                $headers = array_map('trim', fgetcsv($handle, 1000, ','));
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($headers) === count($data)) {
                        $row = array_combine($headers, $data);
                        $this->prepareRow($row, $recordsToInsert);
                    }
                }
                fclose($handle);
            }
        }

        if (empty($recordsToInsert)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid records found in the uploaded file.'
            ], 422);
        }

        // 3. Batch execute bulk upsert inside a single transaction
        DB::connection('cpr_cdrrhr')->transaction(function () use ($recordsToInsert) {
            $chunks = array_chunk($recordsToInsert, 500);

            foreach ($chunks as $chunk) {
                DB::connection('cpr_cdrrhr')
                    ->table('CDRRHR_CPR_MEDICAL_DEVICES')
                    ->upsert(
                        $chunk,
                        ['registration_number'], // Unique key column
                        [                        // Columns to update if row exists
                            'product_name',
                            'manufacturer',
                            'country_of_origin',
                            'trader',
                            'distributor',
                            'issuance_date',
                            'expiry_date',
                            'is_canceled'
                        ]
                    );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'File records successfully imported/updated!'
        ]);
    }

    /**
     * Helper to map file headers to database columns
     */
    private function prepareRow(array $row, array &$recordsToInsert): void
    {
        $CPRNumber = trim($row['registration_number'] ?? '');

        if (!empty($CPRNumber)) {
            $recordsToInsert[] = [
                'registration_number' => $CPRNumber,
                'product_name'        => $row['product_name'] ?? null,
                'manufacturer'        => $row['manufacturer'] ?? null,
                'country_of_origin'   => $row['country_of_origin'] ?? null,
                'trader'              => $row['trader'] ?? null,
                'distributor'         => $row['distributor'] ?? null,
                'issuance_date'       => $row['issuance_date'] ?? null,
                'expiry_date'         => $row['expiry_date'] ?? null,
                'is_canceled'         => $row['is_canceled'] ?? 'N',
            ];
        }
    }
}