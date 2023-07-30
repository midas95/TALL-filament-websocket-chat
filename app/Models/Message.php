<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];



    // Relations
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answeredMessage(){
        return $this->belongsTo(Message::class);
    }

    public function activity(){
        return $this->belongsTo(Activity::class);
    }



    // MorphMany Relations
    public function medias(){
        return $this->morphMany(Media::class, 'model');
    }
}
