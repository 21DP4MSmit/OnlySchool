<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timetable;  // Import models if needed
use App\Models\Mark;  // Assuming you have a Mark model
use App\Models\Absence;  // Assuming you have an Absence model

class DashboardController extends Controller
{
    // Fetch today's timetable
    public function getTodayTimetable()
    {
        $timetable = Timetable::whereDate('date', today())->get();  // Assuming the model has a 'date' column
        return response()->json($timetable);
    }

    // Fetch recent marks
    public function getRecentMarks()
    {
        $marks = Mark::latest()->take(5)->get();  // Example: Get the latest 5 marks
        return response()->json($marks);
    }

    // Fetch monthly absences
    public function getMonthlyAbsences()
    {
        $absences = Absence::whereMonth('date', now()->month)->get();  // Get absences for the current month
        return response()->json($absences);
    }
}
