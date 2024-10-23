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
        $class = Classroom::where('classID', $classroomId)->first();  // Check if the classroom exists

        if ($class) {
            $users = User::where('class_id', $class->id)->get();  // Ensure the correct class_id is used

            if ($users->isEmpty()) {
                return response()->json(['message' => 'No students found for this class.'], 404);
            }

            return response()->json($users);  // Return users for the class
        } else {
            return response()->json(['message' => 'Class not found.'], 404);
        }
    } catch (\Exception $e) {
        \Log::error('Error fetching users by class: ' . $e->getMessage());

        return response()->json(['error' => 'An error occurred while fetching users.'], 500);
    }
}
}
