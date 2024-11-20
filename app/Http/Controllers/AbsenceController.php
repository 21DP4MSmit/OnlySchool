<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
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

    public function saveAbsences(Request $request)
    {
        try {
            \Log::info('Saving absences request data:', $request->all());

            $validatedData = $request->validate([
                'subject_id' => 'required|integer',
                'class_id' => 'required|integer',
                'date' => 'required|date',
                'absences' => 'required|array',
                'absences.*' => 'nullable|in:1,2',
                'atzimes' => 'array',
                'atzimes.*' => 'nullable|string|in:0,1,2,3,4,5,6,7,8,9,10,n/v', // Validate grades
            ]);

            foreach ($validatedData['absences'] as $userId => $absence) {
                if ($absence === null) {
                    // Remove absence entry if there's no data
                    Absence::where([
                        ['UserID', '=', $userId],
                        ['SubjectID', '=', $validatedData['subject_id']],
                        ['date', '=', $validatedData['date']],
                    ])->delete();
                } else {
                    // Insert or update the absence
                    Absence::updateOrInsert(
                        [
                            'UserID' => $userId,
                            'SubjectID' => $validatedData['subject_id'],
                            'date' => $validatedData['date'],
                        ],
                        [
                            'Absence' => $absence,
                            'updated_at' => now(),
                        ]
                    );
                }
            }

            // Handle grades (atzimes)
            foreach ($validatedData['atzimes'] as $userId => $grade) {
                $gradeValue = ($grade === 'n/v') ? null : $grade;

                Mark::updateOrInsert(
                    [
                        'UserID' => $userId,
                        'SubjectID' => $validatedData['subject_id'],
                        'date' => $validatedData['date'],
                    ],
                    [
                        'mark' => $gradeValue,
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json(['message' => 'Absences and grades updated successfully'], 200);
        } catch (\Exception $e) {
            \Log::error('Error updating absences and grades: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating absences and grades.'], 500);
        }
    }
}
