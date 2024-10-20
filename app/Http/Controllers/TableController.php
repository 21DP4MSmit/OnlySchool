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
        $table = $request->input('table');
        $data = $request->except(['table', '_token']);

        // Insert data into the selected table
        DB::table($table)->insert($data);

        return back()->with('success', 'Data inserted successfully!');
    }
}

