<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_location',
        'position_slug'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
    
    public function position()
    {
        // 'position_slug' is the foreign key in the 'images' table that references 'slug' in 'positions' table
        return $this->belongsTo(Position::class, 'position_slug', 'slug');
    }
}
