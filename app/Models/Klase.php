<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klase extends Model
{
    use HasFactory;

    protected $table = 'klase';

    protected $fillable = ['Department'];

    public function subjectLists()
    {
        return $this->hasMany(SubjectList::class, 'ClassID');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'class_id');
    }
}
