<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class CSLLotReleaseController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('csl')
            ->table('CSL_LOT_RELEASE');

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('lot_release_number', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('batch_lot_number', 'like', "%{$search}%");
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
        DB::connection('csl')
            ->table('CSL_LOT_RELEASE')
            ->insert([
                'lot_release_number'        => $request->lot_release_number,
                'generic_name'              => $request->generic_name,
                'brand_name'                => $request->brand_name,
                'dosage_strength'           => $request->dosage_strength,
                'dosage_form'               => $request->dosage_form,
                'registration_number'       => $request->registration_number,
                'batch_lot_number'          => $request->batch_lot_number,
                'packaging_lot'             => $request->packaging_lot,
                'issuance_date'             => $request->issuance_date,
                'expiry_date'               => $request->expiry_date,
                'is_canceled'               => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('csl')
            ->table('CSL_LOT_RELEASE')
            ->where('lot_release_number', $id)
            ->update([
                'generic_name'          => $request->generic_name,
                'brand_name'            => $request->brand_name,
                'dosage_strength'       => $request->dosage_strength,
                'dosage_form'           => $request->dosage_form,
                'registration_number'   => $request->registration_number,
                'batch_lot_number'      => $request->batch_lot_number,
                'packaging_lot'         => $request->packaging_lot,
                'issuance_date'         => $request->issuance_date,
                'expiry_date'           => $request->expiry_date,
                'is_canceled'           => $request->is_canceled ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('csl')
            ->table('CSL_LOT_RELEASE')
            ->where('lot_release_number', $id)
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
            $lrNumber = trim($row['lot_release_number'] ?? '');

            if (empty($lrNumber)) {
                continue;
            }

            DB::connection('csl')
                ->table('CSL_LOT_RELEASE')
                ->updateOrInsert(
                    ['lot_release_number' => $lrNumber], 
                    [
                        'generic_name'        => $row['generic_name'] ?? null,
                        'brand_name'          => $row['brand_name'] ?? null,
                        'dosage_strength'     => $row['dosage_strength'] ?? null,
                        'dosage_form'         => $row['dosage_form'] ?? null,
                        'registration_number' => $row['registration_number'] ?? null,
                        'batch_lot_number'    => $row['batch_lot_number'] ?? null,
                        'packaging_lot'       => $row['packaging_lot'] ?? null,
                        'issuance_date'       => $row['issuance_date'] ?? null,
                        'expiry_date'         => $row['expiry_date'] ?? null,
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