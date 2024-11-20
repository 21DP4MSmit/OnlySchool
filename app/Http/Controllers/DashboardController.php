<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubjectList;
use App\Models\Mark;
use App\Models\Absence;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $classId = $user->class_id;
        $today = Carbon::today();

        // Fetch today's timetable
        $todayTimetable = SubjectList::where('ClassID', $classId)
            ->whereDate('Date', $today)
            ->with('subject', 'classroom')
            ->get();

        // Fetch recent grades
        $recentGrades = Mark::where('UserID', $user->id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->limit(3)
            ->get();

        // Fetch recent absences
        $recentAbsences = Absence::where('UserID', $user->id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->limit(3)
            ->get();

        // Calculate average grade for the current month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $marks = Mark::where('UserID', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->pluck('mark')
            ->filter(function ($value) {
                return is_numeric($value);
            });

        $averageGrade = $marks->count() > 0 ? $marks->avg() : 0;

        return Inertia::render('Dashboard', [
            'todayTimetable' => $todayTimetable,
            'recentGrades' => $recentGrades,
            'recentAbsences' => $recentAbsences,
            'averageGrade' => $averageGrade,
        ]);
    }
}
