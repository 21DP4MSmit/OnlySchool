<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function getTodayTimetable(Request $request)
    {
        $studentId = auth()->user()->id; 

        $todayTimetable = DB::table('classrooms')
            ->join('subjects', 'classrooms.subject_id', '=', 'subjects.id')
            ->whereDate('classrooms.date', Carbon::today())
            ->where('classrooms.student_id', $studentId)
            ->select('subjects.name as subject', 'classrooms.time')
            ->get();

        return response()->json($todayTimetable);
    }

    public function getRecentGrades(Request $request)
    {
        $studentId = auth()->user()->id;

        $recentGrades = DB::table('marks')
            ->join('subjects', 'marks.subject_id', '=', 'subjects.id')
            ->where('marks.student_id', $studentId)
            ->orderBy('marks.created_at', 'desc')
            ->limit(5)
            ->select('subjects.name as subject', 'marks.grade', 'marks.created_at as date')
            ->get();

        return response()->json($recentGrades);
    }

    public function getMonthlyAbsences(Request $request)
    {
        $studentId = auth()->user()->id;
        $startOfMonth = Carbon::now()->startOfMonth();

        $monthlyAbsences = DB::table('absences')
            ->where('student_id', $studentId)
            ->where('date', '>=', $startOfMonth)
            ->select('date', 'reason')
            ->get();

        return response()->json($monthlyAbsences);
    }
}
