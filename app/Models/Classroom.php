<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $primaryKey = 'ClassroomID';

    protected $fillable = [
        'classID',
        'studentID',
    ];

    public function class()
    {
        return $this->belongsTo(Klase::class, 'ClassID');
    }
    public function student()
    {
        return $this->belongsTo(User::class, 'StudentID');
    }

}
