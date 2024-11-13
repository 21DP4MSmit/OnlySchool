<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    /**
     * Method for students to view their absences.
     */
    public function index()
{
    $userId = Auth::id();

    $absences = Absence::where('UserID', $userId)
        ->with(['subject', 'classroom']) 
        ->orderBy('date', 'desc')
        ->get();

    return inertia('Kavejumi/Index', [
        'absences' => $absences
    ]);
}
}