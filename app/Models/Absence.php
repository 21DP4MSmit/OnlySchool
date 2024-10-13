<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $primaryKey = 'AbsenceID';

    protected $fillable = [
        'absenceDate',
        'reason',
        'UserID',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID');
    }
}
