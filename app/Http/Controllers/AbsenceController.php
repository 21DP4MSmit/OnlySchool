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
                'date' => 'required|date',  
                'absences' => 'required|array',
                'absences.*' => 'nullable|in:1,2', 
            ]);

            foreach ($validatedData['absences'] as $userId => $absence) {
                if ($absence === null) {
                    Absence::where([
                        ['UserID', '=', $userId],
                        ['SubjectID', '=', $validatedData['subject_id']],
                        ['date', '=', $validatedData['date']],
                    ])->delete();
                } else {
                    Absence::updateOrCreate(
                        [
                            'UserID' => $userId,
                            'SubjectID' => $validatedData['subject_id'],
                            'date' => $validatedData['date'],  
                        ],
                        [
                            'Absence' => $absence,
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
