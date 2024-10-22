<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['Name'];

    public function subjectLists()
    {
        return $this->hasMany(SubjectList::class, 'SubjectID');
    }
}
