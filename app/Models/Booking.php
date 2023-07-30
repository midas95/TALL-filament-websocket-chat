<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = [];


    // MorphOne Relations
    public function conversation(){
        return $this->morphOne(Conversation::class, 'conversationable');
    }

    // Relations

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function cinema()
    {
        return $this->belongsTo(Biz::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Biz::class);
    }

//    public function programs()
//    {
//        return $this->hasMany(Program::class);
//    }

}
