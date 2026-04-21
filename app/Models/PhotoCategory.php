<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhotoCategory extends Model
{
    use SoftDeletes;

    protected $table = 'photo_categories';

    protected $fillable = ['name', 'slug'];

    public function photos(): HasMany
    {
        // Явно указываем foreign key
        return $this->hasMany(Photo::class, 'category_id');
    }
}
