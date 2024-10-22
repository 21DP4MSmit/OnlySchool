<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectList;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DienasgramataController extends Controller
{
    

    public function index(Request $request)
{
    // Get the user's class ID
    $classId = Auth::user()->class_id;

    // If a week start date is provided, use it, else use the current week
    $weekStart = $request->input('weekStart') 
                ? Carbon::parse($request->input('weekStart'))
                : Carbon::now()->startOfWeek(Carbon::MONDAY);
    
    // Set the end of the week (Friday)
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);
    
    // Fetch subject lists for the specified week
    $subjectLists = SubjectList::where('ClassID', $classId)
                        ->whereBetween('Date', [$weekStart, $weekEnd])
                        ->with('subject', 'classroom', 'marks', 'absences')
                        ->get();

    return inertia('Dienasgramata/Index', [
        'subjectLists' => $subjectLists,
        'weekStart' => $weekStart->toDateString(), // Pass weekStart back to the frontend
    ]);
}
    



}
