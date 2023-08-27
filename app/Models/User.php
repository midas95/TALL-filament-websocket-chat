<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{

    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'is_admin',
        'email',
        'password',
        'activity'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activity' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function conversations(){
        return Conversation::where('type', 'private')->where(function($q){
            $q->where('participant_a_id', $this->id)
                ->orWhere('participant_b_id', $this->id);
        });
    }

    public function conversationsWithInterlocutorName(){
        return Conversation::select('conversations.*', 'users.id as interlocutorId', 'users.name as interlocutorName', 'users.activity as interlocutorActivity')
            ->join('users', function($join) {
                $join->on('users.id', '=', DB::raw('(case when participant_a_id = '.auth()->user()->id.' then participant_b_id else participant_a_id end)'));
            })
            ->where('type', 'private')
            ->where(function($q){
                $q->where('participant_a_id', $this->id)
                ->orWhere('participant_b_id', $this->id);
            });
    }

    public function canAccessFilament(): bool {
        return true;
//        return str_ends_with($this->email, '@admin.com' );
    }

    public function messages(){ return $this->morphMany(Info::class, 'infoable'); }


    public function getAvatarUrl(): string
    {
        $name = Str::of($this->name)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827';
    }

    public function isOnline(): bool
    {
       return $this->activity->diffInMinutes() < 5;
    }

}
