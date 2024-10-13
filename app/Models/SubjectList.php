<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectList extends Model
{
    use HasFactory;

    protected $primaryKey = 'SubjectListID';

    protected $fillable = [
        'SubjectID',
        'ClassID',
    ];

    // Relationships
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubjectID');
    }

    public function class()
    {
        return $this->belongsTo(Klase::class, 'ClassID');
    }
}
