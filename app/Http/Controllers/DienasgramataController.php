<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\SubjectList;
use App\Models\Absence;
use App\Models\Mark;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class DienasgramataController extends Controller
{
    public function index(Request $request)
    {
        $classId = Auth::user()->class_id;

        $weekStart = $request->input('weekStart') 
                    ? Carbon::parse($request->input('weekStart'))
                    : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        $subjectLists = SubjectList::where('ClassID', $classId)
                     ->whereBetween('Date', [$weekStart, $weekEnd])
                     ->with('subject', 'classroom', 'marks', 'absences')
                     ->get();

\Log::info('SubjectLists fetched:', $subjectLists->toArray()); 

return inertia('Dienasgramata/Index', [
    'subjectLists' => $subjectLists,
    'weekStart' => $weekStart->toDateString(),
]);
    }

    public function fetchSubjectLists(Request $request, $viewName)
    {
        try {
            $classId = Auth::user()->class_id;
            
            $weekStart = $request->input('weekStart') 
                        ? Carbon::parse($request->input('weekStart')) 
                        : Carbon::now()->startOfWeek(Carbon::MONDAY);

            $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

            $subjectLists = SubjectList::where('ClassID', $classId)
                                ->whereBetween('Date', [$weekStart, $weekEnd])
                                ->with(['subject', 'classroom', 'marks', 'absences'])
                                ->get();

            \Log::info('Fetched subject lists:', ['subjectLists' => $subjectLists]);

            return inertia($viewName, [
                'subjectLists' => $subjectLists,
                'weekStart' => $weekStart->toDateString(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching subject lists: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch subject lists.'], 500);
        }
    }

public function teacherClasses(Request $request)
{
    try {
        $weekStart = $request->input('weekStart') 
            ? Carbon::parse($request->input('weekStart'))
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
        $userId = Auth::user()->id;

        $subjectLists = SubjectList::whereBetween('Date', [$weekStart, $weekEnd])
            ->whereHas('classroom', function($query) use ($userId) {
                $query->where('UserID', $userId);
            })
            ->with('subject', 'classroom', 'marks', 'absences', 'klase')
            ->get();

        \Log::info('Subject Lists:', ['subjectLists' => $subjectLists]);

        return inertia('TeacherClasses/Index', [
            'classes' => $subjectLists,
            'weekStart' => $weekStart->toDateString(),
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching teacher classes: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch teacher classes.'], 500);
    }
}

public function todayClasses(Request $request)
{
    try {
        $today = Carbon::today();
        $userId = Auth::user()->id;

        $subjectLists = SubjectList::whereDate('Date', $today)
            ->whereHas('classroom', function($query) use ($userId) {
                $query->where('UserID', $userId);
            })
            ->with('subject', 'classroom', 'marks', 'absences', 'klase')
            ->get();

        \Log::info('Today’s Classes:', ['subjectLists' => $subjectLists]);

        return inertia('TeacherDashboard/TodayIndex', [
            'teacherClasses' => $subjectLists->toArray(),
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching today’s classes: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch today’s classes.'], 500);
    }
}

public function teacherDashboard(Request $request)
{
    // Fetch the classes data you want for the dashboard, such as today’s classes
    // Adjust this as necessary based on your data requirements
    $userId = Auth::user()->id;
    $today = Carbon::today();

    $subjectLists = SubjectList::whereDate('Date', $today)
        ->whereHas('classroom', function($query) use ($userId) {
            $query->where('UserID', $userId);
        })
        ->with(['subject', 'classroom'])
        ->get();

    return Inertia::render('TeacherDashboard/TodayIndex', [
        'teacherClasses' => $subjectLists->toArray(),
    ]);
}


public function teacherAbsences(Request $request)
{
    try {
        $userId = Auth::user()->id;
        $weekStart = $request->input('weekStart')
            ? Carbon::parse($request->input('weekStart'))
            : Carbon::now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        $subjectLists = SubjectList::whereBetween('Date', [$weekStart, $weekEnd])
            ->whereHas('classroom', function ($query) use ($userId) {
                $query->where('UserID', $userId);
            })
            ->with('subject', 'classroom', 'marks', 'absences', 'klase')
            ->get();

        $absenceData = Absence::whereBetween('date', [$weekStart, $weekEnd])
            ->whereIn('SubjectID', $subjectLists->pluck('SubjectID'))
            ->get();

        $markData = Mark::whereBetween('date', [$weekStart, $weekEnd])
            ->whereIn('SubjectID', $subjectLists->pluck('SubjectID'))
            ->get();

        return inertia('TeacherAbsences/Index', [
            'subjectLists' => $subjectLists,
            'weekStart' => $weekStart->toDateString(),
            'absenceData' => $absenceData,
            'markData' => $markData,
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching teacher absences: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch teacher absences.'], 500);
    }
}

}
