<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{

    protected $fillable = [
        'student_id',
        'status',
        'date',
        'notes',
        'user_id',
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public  function user()
    {
        return $this->belongsTo(User::class);
    }
}
