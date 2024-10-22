<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubjectList;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DienasgramataController extends Controller
{
    public function index()
{
    $classId = Auth::user()->class_id;

    // Fetch subject lists for the current week for the user's class (from Monday to Friday)
    $subjectLists = SubjectList::where('ClassID', $classId)
                        ->whereBetween('Date', [now()->startOfWeek(Carbon::MONDAY), now()->endOfWeek(Carbon::FRIDAY)])
                        ->with(['subject', 'classroom', 'marks', 'absences'])
                        ->orderBy('Date', 'asc') // Order by date ascending
                        ->get();

    return inertia('Dienasgramata/Index', [
        'subjectLists' => $subjectLists
    ]);
}


}
