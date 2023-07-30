<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = null;

    // MorphTo Relations
    public function conversationable()
    {
        return $this->morphTo();
    }

    // Relations
    public function massages()
    {
        return $this->hasMany(Message::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function interlocutor(){
        if(in_array($this->type,  ['biz', 'booking'])){
            return $this->user()->current_biz_id == $this->participant_a_id ? Biz::find($this->participant_b_id) : Biz::find($this->participant_a_id);
        }
        if($this->type == 'private'){
            return $this->user()->id == $this->participant_a_id ? User::find($this->participant_b_id) : User::find($this->participant_a_id);
        }
    }

    public function participants(){
        if(in_array($this->type,  ['biz', 'booking'])){
            return [
                Biz::find($this->participant_a_id),
                Biz::find($this->participant_b_id)
            ];
        }
        if($this->type == 'private'){
            return [
                User::find($this->participant_a_id),
                User::find($this->participant_b_id)
            ];
        }
    }
}
