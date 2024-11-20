<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $primaryKey = 'MarkID'; // Set the primary key if it's not 'id'
    public $incrementing = true; // Set this if your primary key is auto-incrementing

    protected $fillable = ['UserID', 'SubjectID', 'mark', 'date'];

    // Define the subject relationship
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubjectID', 'SubjectID');
    }

    // If you still need the subjectList relationship
    public function subjectList()
    {
        return $this->belongsTo(SubjectList::class, 'SubjectID', 'SubjectID');
    }
}
