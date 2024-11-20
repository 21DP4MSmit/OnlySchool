<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'SubjectID'; // Set the primary key if it's not 'id'
    public $incrementing = true; // Set this if your primary key is auto-incrementing

    protected $fillable = ['Name'];

    public function marks()
    {
        return $this->hasMany(Mark::class, 'SubjectID', 'SubjectID');
    }

    // If you need the subjectLists relationship
    public function subjectLists()
    {
        return $this->hasMany(SubjectList::class, 'SubjectID', 'SubjectID');
    }
}
