<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = ['UserID', 'SubjectID', 'Absence', 'date'];
    public function subject()
{
    return $this->belongsTo(Subject::class, 'SubjectID', 'SubjectID');
}

public function classroom()
{
    return $this->belongsTo(Classroom::class, 'ClassroomID', 'id');
}

}
