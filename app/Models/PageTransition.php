<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_visit_id',
        'from_url',
        'to_url',
        'transition_at',
    ];

    protected $casts = [
        'transition_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function visit()
    {
        return $this->belongsTo(UserVisit::class);
    }
}
