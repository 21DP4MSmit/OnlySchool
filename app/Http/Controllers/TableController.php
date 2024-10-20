<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index()
    {
        // Fetch all table names from the database
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn($table) => array_values((array)$table)[0], $tables);
        return Inertia::render('TableManager', [
            'tables' => $tableNames,
        ]);
    }

    public function fetchData($table)
    {
        // Fetch data from the selected table
        $data = DB::table($table)->get();

        return response()->json($data);
    }

    public function insert(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            // Add validation rules for your table fields
        ]);
    
        $table = $request->input('table');
        $data = $request->except(['table', '_token']);
    
        // Log the incoming data to see its structure
        \Log::info('Data being inserted:', $data);
    
        // Check if $data is an array
        if (!is_array($data)) {
            return response()->json(['message' => 'Invalid data format.'], 400);
        }
    
        try {
            // Insert data into the selected table
            DB::table($table)->insert($data);
            return response()->json(['message' => 'Data inserted successfully!']);
        } catch (\Exception $e) {
            // Log the error for further analysis
            \Log::error('Insert failed:', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return response()->json(['message' => 'Failed to insert data: ' . $e->getMessage()], 500);
        }
    }
    
    
}

