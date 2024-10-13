<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'SubjectID';

    protected $fillable = [
        'subjectName',
    ];

    // Relationships
    public function subjectList()
    {
        return $this->hasMany(SubjectList::class, 'SubjectID');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class, 'SubjectID');
    }
}
