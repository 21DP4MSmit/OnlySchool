<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Import for password hashing
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index()
    {
        // Fetch all table names from the database
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn($table) => array_values((array)$table)[0], $tables);

        // Exclude specific tables
        $excludeTables = ['cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'messages', 'migrations', 'password_reset_tokens', 'sessions'];
        $filteredTables = array_filter($tableNames, fn($table) => !in_array($table, $excludeTables));

        return Inertia::render('TableManager', [
            'tables' => array_values($filteredTables),
        ]);
    }

    public function fetchData($table)
    {
        // Fetch data from the selected table
        $data = DB::table($table)->get();

        return response()->json($data);
    }

    // New method for inserting into the users table
    public function insertUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
            'phoneNumber' => 'nullable|string|max:20',
            'class_id' => 'nullable|integer',
        ]);

        try {
            // Insert user data with hashed password and timestamp fields
            DB::table('users')->insert([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')), // Hashing the password
                'role' => $request->input('role'),
                'phoneNumber' => $request->input('phoneNumber'),
                'class_id' => $request->input('class_id'),
                'created_at' => now(), // Setting created_at to now
                'updated_at' => now(), // Setting updated_at to now
            ]);

            return response()->json(['message' => 'User inserted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to insert user: ' . $e->getMessage()], 500);
        }
    }

    public function insert(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
        ]);

        $table = $request->input('table');
        $data = $request->except(['table', '_token']);

        try {
            // Insert data into the selected table
            DB::table($table)->insert($data);
            return response()->json(['message' => 'Data inserted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to insert data: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $table, $id)
    {
        $data = $request->except(['_token']);

        try {
            // Update the entry in the table
            DB::table($table)->where('id', $id)->update($data);
            return response()->json(['message' => 'Data updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update data: ' . $e->getMessage()], 500);
        }
    }
}
