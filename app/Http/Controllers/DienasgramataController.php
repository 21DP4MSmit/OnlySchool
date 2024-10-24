<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\SubjectList;
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
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        // Get the currently logged-in user ID
        $userId = Auth::user()->id;

        // Only select the classrooms that match the current user's ID
        $subjectLists = SubjectList::whereBetween('Date', [$weekStart, $weekEnd])
            ->whereHas('classroom', function($query) use ($userId) {
                $query->where('UserID', $userId);
            })
            ->with('subject', 'classroom', 'marks', 'absences')
            ->get();

        \Log::info('Subject Lists:', ['subjectLists' => $subjectLists]);

        return inertia('TeacherClasses/Index', [
            'classes' => $subjectLists,  // Pass filtered subject lists
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching teacher classes: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch teacher classes.'], 500);
    }
}

public function todayClasses(Request $request)
{
    $teacherId = Auth::id(); 
    $today = Carbon::today(); 

    $todayClasses = SubjectList::whereHas('classroom', function ($query) use ($teacherId) {
        $query->where('UserID', $teacherId); 
    })
    ->whereDate('Date', $today) 
    ->with(['subject', 'classroom']) 
    ->get();

    return inertia('TeacherDashboard/Index', [
        'todayClasses' => $todayClasses->toArray(), 
    ]);
}


public function teacherDashboard(Request $request)
{
    $today = Carbon::today();
    $teacherId = Auth::id(); 

    $todayClasses = SubjectList::whereHas('classroom', function($query) use ($teacherId) {
            $query->where('UserID', $teacherId); 
        })
        ->whereDate('Date', $today) 
        ->with(['subject', 'classroom']) 
        ->get();

    return Inertia::render('TeacherDashboard/Index', [
        'todayClasses' => $todayClasses,
    ]);
}
public function teacherAbsences(Request $request)
    {
        return $this->fetchSubjectLists($request, 'TeacherAbsences/Index');
    }
}
