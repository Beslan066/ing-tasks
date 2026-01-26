<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'size',
        'mime_type',
        'extension',
        'uploaded_by',
        'company_id',
        'department_id',
        'disk',
        'folder',
        'task_id',
        'is_public'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // Связь с задачей
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function emailFiles(): HasMany
    {
        return $this->hasMany(EmailFile::class);
    }
}
