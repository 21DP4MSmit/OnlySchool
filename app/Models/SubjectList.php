<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectList extends Model
{
    use HasFactory;

    protected $fillable = [
        'ClassID', 'SubjectID', 'ClassroomID', 'Date', 'homework', 'topic'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubjectID', 'SubjectID');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'ClassroomID', 'id');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'SubjectID', 'SubjectID')
                    ->where('UserID', auth()->id());
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'SubjectID', 'SubjectID')
                    ->where('UserID', auth()->id());
    }
}



