<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FdaHRController extends Controller
{
    // List all employees
    public function list(Request $request)
    {
        $query = DB::connection('fdaEMP')->table('Employees_Info');

        if ($request->search) {
            $search = strtolower($request->search); // Convert search term to lowercase

            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(EMP_NAME) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(EMP_DESIGNATION) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(OFFICE_CENTER) like ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(ID_NUMBER) like ?', ["%{$search}%"]);
            });
        }

        // pagination
        $employees = $query->orderBy('EMP_NAME', 'asc')->paginate(10);

        return response()->json($employees);
    }

    // Add new employee
    public function add(Request $request)
    {  
        $request->validate([
            'ID_NUMBER' => 'required|unique:fdaEMP.Employees_Info,ID_NUMBER',
            'EMP_NAME' => 'required',
            'EMP_DESIGNATION' => 'required',
            'OFFICE_CENTER' => 'required',
            'STATUS' => 'required',
            'EMP_PICTURE' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
        ], [], [
            'ID_NUMBER' => 'ID Number',
            'EMP_NAME' => 'Employee Name',
            'EMP_DESIGNATION' => 'Employee Designation',
            'OFFICE_CENTER' => 'Office Center',
            'STATUS' => 'Status',
            'EMP_PICTURE' => 'Employee Picture',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('EMP_PICTURE')) {
            $image = $request->file('EMP_PICTURE');
            
            // Create unique filename
            $filename = time() . '_' . $request->ID_NUMBER . '.' . $image->getClientOriginalExtension();
            
            // Store in public/employee_images directory
            $path = $image->storeAs('employee_images', $filename, 'public');
            
            // Get the full URL
            $imagePath = Storage::url($path);
        }

        $addEMP = DB::connection('fdaEMP')->table('Employees_Info')->insert([
            'ID_NUMBER' => $request->ID_NUMBER,
            'EMP_NAME' => $request->EMP_NAME,
            'EMP_DESIGNATION' => $request->EMP_DESIGNATION,
            'OFFICE_CENTER' => $request->OFFICE_CENTER,
            'STATUS' => $request->STATUS,
            'EMP_PICTURE' => $imagePath,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Employee added successfully',
            'image_path' => $imagePath
        ]);
    }

    // Update employee
    public function update(Request $request, $id)
    {
        $request->validate([
            'EMP_NAME' => 'required',
            'EMP_DESIGNATION' => 'required',
            'OFFICE_CENTER' => 'required',
            'STATUS' => 'required',
            'EMP_PICTURE' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
        ], [], [
            'EMP_NAME' => 'Employee Name',
            'EMP_DESIGNATION' => 'Employee Designation',
            'OFFICE_CENTER' => 'Office Center',
            'STATUS' => 'Status',
            'EMP_PICTURE' => 'Employee Picture',
        ]);

        // Get existing employee data
        $employee = DB::connection('fdaEMP')
            ->table('Employees_Info')
            ->where('ID_NUMBER', $id)
            ->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $imagePath = $employee->EMP_PICTURE; // Keep existing image by default

        // Handle new image upload
        if ($request->hasFile('EMP_PICTURE')) {
            // Delete old image if exists
            if ($employee->EMP_PICTURE) {
                $oldPath = str_replace('/storage/', '', $employee->EMP_PICTURE);
                Storage::disk('public')->delete($oldPath);
            }

            $image = $request->file('EMP_PICTURE');
            $filename = time() . '_' . $id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('employee_images', $filename, 'public');
            $imagePath = Storage::url($path);
        }

        DB::connection('fdaEMP')->table('Employees_Info')
            ->where('ID_NUMBER', $id)
            ->update([
                'EMP_NAME' => $request->EMP_NAME,
                'EMP_DESIGNATION' => $request->EMP_DESIGNATION,
                'OFFICE_CENTER' => $request->OFFICE_CENTER,
                'STATUS' => $request->STATUS,
                'EMP_PICTURE' => $imagePath,
            ]);

        return response()->json([
            'success' => true, 
            'message' => 'Employee updated successfully',
            'image_path' => $imagePath
        ]);
    }

    public function view($id)
{
    try {
        $employee = DB::connection('fdaEMP')
            ->table('Employees_Info')
            ->where('ID_NUMBER', $id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json($employee);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching employee details',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function download()
    {
        $employees = DB::connection('fdaEMP')
            ->table('Employees_Info')
            ->orderBy('EMP_NAME', 'asc')
            ->get();

        $filename = "FDA_Employees_" . date("Y-m-d_H-i-s") . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ["ID_NUMBER", "EMP_NAME", "EMP_DESIGNATION", "OFFICE_CENTER", "STATUS", "EMP_PICTURE"];

        $callback = function() use ($employees, $columns) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, $columns);

            // Data rows
            foreach ($employees as $emp) {
                fputcsv($file, [
                    $emp->ID_NUMBER,
                    $emp->EMP_NAME,
                    $emp->EMP_DESIGNATION,
                    $emp->OFFICE_CENTER,
                    $emp->STATUS,
                    $emp->EMP_PICTURE,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
