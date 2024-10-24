<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; 

    protected $fillable = [
        'ClassID',
        'UserID',
        'Classroom',
    ];


    public function class()
    {
        return $this->belongsTo(Klase::class, 'ClassID');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function subjectLists()
    {
        return $this->hasMany(SubjectList::class, 'ClassroomID', 'id');
    }
}
