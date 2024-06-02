<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'total_break',
        'work_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }
}
