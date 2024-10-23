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
                'date' => 'required|date',  // Ensure correct date format
                'absences' => 'required|array',
                'absences.*' => 'nullable|in:1,2',  // Absences should be either null, 1 (justified), or 2 (unjustified)
            ]);

            foreach ($validatedData['absences'] as $userId => $absence) {
                if ($absence === null) {
                    // If absence is null, delete the absence record
                    Absence::where([
                        ['UserID', '=', $userId],
                        ['SubjectID', '=', $validatedData['subject_id']],
                        ['date', '=', $validatedData['date']],
                    ])->delete();
                } else {
                    // Update or create absence
                    Absence::updateOrCreate(
                        [
                            'UserID' => $userId,
                            'SubjectID' => $validatedData['subject_id'],
                            'date' => $validatedData['date'],  // Use the date from the request
                        ],
                        [
                            'Absence' => $absence,  // Set the absence status
                        ]
                    );
                }
            }

            return response()->json(['message' => 'Absences updated successfully'], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating absences: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating absences.'], 500);
        }
    }
}
