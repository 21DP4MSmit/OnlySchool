<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    // Add this method to handle TeacherAbsences
    public function teacherAbsences(Request $request)
    {
        return $this->fetchSubjectLists($request, 'TeacherAbsences/Index');
    }
}
