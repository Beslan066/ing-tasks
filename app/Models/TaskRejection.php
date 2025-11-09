<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskRejection extends Model
{
    use HasFactory;

    protected $fillable = [
        'reason',
        'task_id',
        'user_id',
        'company_id'
    ];

    /**
     * Задача, от которой отказались
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Пользователь, который отказался
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Компания
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
