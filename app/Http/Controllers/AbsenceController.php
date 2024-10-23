<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function saveAbsences(Request $request)
{
    try {
        $validatedData = $request->validate([
            'subject_id' => 'required|integer',
            'class_id' => 'required|integer',
            'absences' => 'required|array',
            'absences.*' => 'nullable|in:1,2', 
        ]);

        foreach ($validatedData['absences'] as $userId => $absence) {
            if ($absence) {
                Absence::updateOrCreate(
                    [
                        'UserID' => $userId,
                        'SubjectID' => $validatedData['subject_id'],
                        'date' => now(), // Or a date you want to pass for the absence
                    ],
                    [
                        'Absence' => $absence
                    ]
                );
            }
        }

        return response()->json(['message' => 'Absences saved successfully'], 200);
    } catch (\Exception $e) {
        // Log the error
        \Log::error('Error saving absences: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while saving absences.'], 500);
    }
}
}
