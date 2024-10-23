<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectList;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class DienasgramataController extends Controller
{
    // For Dienasgramata
    public function index(Request $request)
    {
        return $this->fetchSubjectLists($request, 'Dienasgramata/Index');
    }

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
    



}
