<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    // Get today's timetable
    public function getTodayTimetable(Request $request)
    {
        $studentId = auth()->user()->id; // Assuming the student is authenticated

        // Fetch today's timetable based on student's ID and today's date
        $todayTimetable = DB::table('classrooms')
            ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id')
            ->whereDate('classrooms.date', Carbon::today())
            ->where('classrooms.student_id', $studentId)
            ->select('subjects.name as subject', 'classrooms.time')
            ->get();

        return response()->json($todayTimetable);
    }

    // Get 4-5 most recent grades
    public function getRecentGrades(Request $request)
    {
        $studentId = auth()->user()->id;

        // Fetch the most recent grades for the authenticated student
        $recentGrades = DB::table('marks')
            ->join('subjects', 'marks.subject_id', '=', 'subjects.id')
            ->where('marks.student_id', $studentId)
            ->orderBy('marks.created_at', 'desc')
            ->limit(5)
            ->select('subjects.name as subject', 'marks.grade', 'marks.created_at as date')
            ->get();

        return response()->json($recentGrades);
    }

    // Get student's absences for the current month
    public function getMonthlyAbsences(Request $request)
    {
        $studentId = auth()->user()->id;
        $startOfMonth = Carbon::now()->startOfMonth();

        // Fetch absences for the current month
        $monthlyAbsences = DB::table('absences')
            ->where('student_id', $studentId)
            ->where('date', '>=', $startOfMonth)
            ->select('date', 'reason')
            ->get();

        return response()->json($monthlyAbsences);
    }
}
