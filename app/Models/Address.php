<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];


    // MorphTo Relations
    public function addressable()
    {
        return $this->morphTo();
    }

    // MorphToMany Relations
    public function tags(){ return $this->morphToMany(Tag::class, 'taggable'); }
}
