<?php

namespace App\Http\Controllers\CCHUHSRR;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;

class eportalLTOController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = DB::connection('lto_eportal')
            ->table('PMT_ELTO_DATA_38');

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ACCOUNTCODE', 'like', "%{$search}%")
                  ->orWhere('NAME_OF_ESTABLISHMENT', 'like', "%{$search}%");
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
        DB::connection('lto_eportal')
            ->table('PMT_ELTO_DATA_38')
            ->insert([
                'APP_UID'   => $request->APP_UID,
                'APP_NUMBER'    => $request->APP_NUMBER,
                'APP_STATUS'    => $request->APP_STATUS,
                'ACCOUNTCODE'   => $request->ACCOUNTCODE,
                'NAME_OF_ESTABLISHMENT' => $request->NAME_OF_ESTABLISHMENT,
                'OWNER_OF_ESTABLISHMENT'    => $request->OWNER_OF_ESTABLISHMENT,
                'ESTABLISHMENT_ADDRESS' => $request->ESTABLISHMENT_ADDRESS,
                'OFFICE_REGION_LABEL'   => $request->OFFICE_REGION_LABEL,
                'LTO_ACTIVITY_LABEL'    => $request->LTO_ACTIVITY_LABEL,
                'PRIMARY_ACTIVITY_LABEL'    => $request->PRIMARY_ACTIVITY_LABEL,
                'SECONDARY_ACTIVITY_LABEL'  => $request->SECONDARY_ACTIVITY_LABEL,
                'LTO_DECISION'  => $request->LTO_DECISION,
                'DECISION_DATE' => $request->DECISION_DATE,
                'DATE_VALIDITY' => $request->DATE_VALIDITY,
                'IS_CANCELED'   => $request->IS_CANCELED ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::connection('lto_eportal')
            ->table('PMT_ELTO_DATA_38')
            ->where('APP_UID', $id)
            ->update([
                'APP_NUMBER'    => $request->APP_NUMBER,
                'APP_STATUS'    => $request->APP_STATUS,
                'ACCOUNTCODE'   => $request->ACCOUNTCODE,
                'NAME_OF_ESTABLISHMENT' => $request->NAME_OF_ESTABLISHMENT,
                'OWNER_OF_ESTABLISHMENT'    => $request->OWNER_OF_ESTABLISHMENT,
                'ESTABLISHMENT_ADDRESS' => $request->ESTABLISHMENT_ADDRESS,
                'OFFICE_REGION_LABEL'   => $request->OFFICE_REGION_LABEL,
                'LTO_ACTIVITY_LABEL'    => $request->LTO_ACTIVITY_LABEL,
                'PRIMARY_ACTIVITY_LABEL'    => $request->PRIMARY_ACTIVITY_LABEL,
                'SECONDARY_ACTIVITY_LABEL'  => $request->SECONDARY_ACTIVITY_LABEL,
                'LTO_DECISION'  => $request->LTO_DECISION,
                'DECISION_DATE' => $request->DECISION_DATE,
                'DATE_VALIDITY' => $request->DATE_VALIDITY,
                'IS_CANCELED'   => $request->IS_CANCELED ?? 'N',
            ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function view($id)
    {
        $record = DB::connection('lto_eportal')
            ->table('PMT_ELTO_DATA_38')
            ->where('APP_UID', $id)
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
            $APPuid = trim($row['APP_UID'] ?? '');

            if (empty($APPuid)) {
                continue;
            }

            DB::connection('lto_eportal')
                ->table('PMT_ELTO_DATA_38')
                ->updateOrInsert(
                    ['APP_UID' => $APPuid], 
                    [
                        'APP_NUMBER'    => $row['APP_NUMBER'] ?? null,
                        'APP_STATUS'    => $row['APP_STATUS'] ?? null,
                        'ACCOUNTCODE'   => $row['ACCOUNTCODE'] ?? null,
                        'NAME_OF_ESTABLISHMENT' => $row['NAME_OF_ESTABLISHMENT'] ?? null,
                        'OWNER_OF_ESTABLISHMENT'    => $row['OWNER_OF_ESTABLISHMENT'] ?? null,
                        'ESTABLISHMENT_ADDRESS' => $row['ESTABLISHMENT_ADDRESS'] ?? null,
                        'OFFICE_REGION_LABEL'   => $row['OFFICE_REGION_LABEL'] ?? null,
                        'LTO_ACTIVITY_LABEL'          => $row['LTO_ACTIVITY_LABEL'] ?? null,
                        'PRIMARY_ACTIVITY_LABEL'     => $row['PRIMARY_ACTIVITY_LABEL'] ?? null,
                        'SECONDARY_ACTIVITY_LABEL'         => $row['SECONDARY_ACTIVITY_LABEL'] ?? null,
                        'LTO_DECISION' => $row['LTO_DECISION'] ?? null,
                        'DECISION_DATE'        => $row['DECISION_DATE'] ?? null,
                        'DATE_VALIDITY'          => $row['DATE_VALIDITY'] ?? null,
                        'IS_CANCELED'         => $row['IS_CANCELED'] ?? 'N',
                    ]
                );
        }

        return response()->json([
            'success' => true,
            'message' => 'File records successfully imported/updated!'
        ]);
    }
}