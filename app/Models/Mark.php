<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $primaryKey = 'MarkID';

    protected $fillable = [
        'Mark',
        'UserID',
        'SubjectID',
        'created_at',
    ];

    // relācijas
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'SubjectID');
    }
}
