<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(fn($table) => array_values((array)$table)[0], $tables);

        $excludeTables = ['cache', 'cache_locks', 'failed_jobs', 'jobs', 'job_batches', 'messages', 'migrations', 'password_reset_tokens', 'sessions'];
        $filteredTables = array_filter($tableNames, fn($table) => !in_array($table, $excludeTables));

        return Inertia::render('TableManager', [
            'tables' => array_values($filteredTables),
        ]);
    }

    public function fetchData($table)
    {
        $data = DB::table($table)->get();
        return response()->json($data);
    }

    public function insertUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,admin',
            'phoneNumber' => 'nullable|string|max:8',
            'class_id' => 'nullable|integer',
        ]);

        try {
            DB::table('users')->insert([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => $request->input('role'),
                'phoneNumber' => $request->input('phoneNumber'),
                'class_id' => $request->input('class_id'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'User inserted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to insert user: ' . $e->getMessage()], 500);
        }
    }

    public function insert(Request $request)
    {
        $table = $request->input('table');
        $data = $request->except(['table', '_token']);

        try {
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
            $primaryKey = $this->getPrimaryKey($table);
            // Saprast vai datos nav prim key
            unset($data[$primaryKey]);
            DB::table($table)->where($primaryKey, $id)->update($data);

            return response()->json(['message' => 'Data updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update data: ' . $e->getMessage()], 500);
        }
    }

    // Helper method to fetch the primary key of a table dynamically
    private function getPrimaryKey($table)
    {
        $primaryKey = DB::selectOne("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
        return $primaryKey->Column_name ?? 'id'; // Fallback to 'id' if no primary key found
    }
}
