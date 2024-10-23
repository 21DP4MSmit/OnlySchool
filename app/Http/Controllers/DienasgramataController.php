<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectList;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class DienasgramataController extends Controller
{

    // For Teacher Absences (separate component)
    public function teacherAbsences(Request $request)
    {
        return $this->fetchSubjectLists($request, 'TeacherAbsences/Index');
    }

    public function index(Request $request)
{
    
    $classId = Auth::user()->class_id;

    // Ja ir dots nedelas sakums, ja nav izmanto current week
    $weekStart = $request->input('weekStart') 
                ? Carbon::parse($request->input('weekStart'))
                : Carbon::now()->startOfWeek(Carbon::MONDAY);
    
    // nedelas beigas = piektdiena
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
    
    // fetcho no db stundas konkretam datumam
    $subjectLists = SubjectList::where('ClassID', $classId)
                        ->whereBetween('Date', [$weekStart, $weekEnd])
                        ->with('subject', 'classroom', 'marks', 'absences')
                        ->get();

    return inertia('Dienasgramata/Index', [
        'subjectLists' => $subjectLists,
        'weekStart' => $weekStart->toDateString(), 
    ]);
}
    

>>>>>>> bab908b1929d86c7875a3722bf6439f08973f942

    // For Teacher Absences (separate component)
    public function teacherAbsences(Request $request)
    {
        return $this->fetchSubjectLists($request, 'TeacherAbsences/Index');
    }

    private function fetchSubjectLists(Request $request, string $component)
    {
        // Fetch authenticated user's class ID
        $classId = Auth::user()->class_id;

        // Start of the week (Monday)
        $weekStart = $request->input('weekStart') 
                    ? Carbon::parse($request->input('weekStart'))
                    : Carbon::now()->startOfWeek(Carbon::MONDAY);

        // End of the week (Friday)
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
        
        // Fetch subject lists for the week
        $subjectLists = SubjectList::where('ClassID', $classId)
                            ->whereBetween('Date', [$weekStart, $weekEnd])
                            ->with('subject', 'classroom', 'marks', 'absences')
                            ->get();

        return Inertia::render($component, [
            'subjectLists' => $subjectLists,
            'weekStart' => $weekStart->toDateString(), 
        ]);
    }
}
