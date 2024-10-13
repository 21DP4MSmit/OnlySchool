<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klase extends Model
{
    use HasFactory;

    protected $primaryKey = 'ClassID';

    protected $fillable = [
        'className',
        'userID',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'ClassID');
    }
}
