<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsersByClass($classroomId)
{
    try {
        $class = Classroom::where('classID', $classroomId)->first();  

        if ($class) {
            $users = User::where('class_id', $class->id)->get();  

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No students found for this class.'], 404);
            }

            return response()->json($users); 
        } else {
            return response()->json(['message' => 'Class not found.'], 404);
        }
    } catch (\Exception $e) {
        \Log::error('Error fetching users by class: ' . $e->getMessage());

        return response()->json(['error' => 'An error occurred while fetching users.'], 500);
    }
}
public function getUserByClass($classId)
{
    try {
        \Log::info("Fetching users for class_id: $classId"); // Debug log

        // Fetch users for the given class_id
        $users = User::where('class_id', $classId)->get();

        if ($users->isEmpty()) {
            \Log::info("No users found for class_id: $classId");
            return response()->json(['message' => 'No students found for this class.'], 404);
        }

        \Log::info("Fetched users: ", $users->toArray());
        return response()->json($users); 
    } catch (\Exception $e) {
        \Log::error('Error fetching users by class: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while fetching users.'], 500);
    }
}
}
