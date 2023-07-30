<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    
    public function model()
    {
        return $this->morphTo();
    }
    
    public function position()
    {
        // 'position_slug' is the foreign key in the 'images' table that references 'slug' in 'positions' table
        return $this->belongsTo(Position::class, 'collection_name', 'slug');
    }
}
