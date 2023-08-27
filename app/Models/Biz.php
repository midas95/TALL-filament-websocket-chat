<?php

namespace App\Models;

use App\Helpers\ChatHelper;
use App\Http\Traits\CinemaTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Biz extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = [];


    public function users(){ return $this->belongsToMany(User::class, 'biz_user'); }
    public function organization(){ return $this->belongsTo(Organization::class); }
    public function sector(){ return $this->belongsTo(Sector::class, 'sector_slug', 'slug'); }
    public function rooms(){ return $this->hasMany(Room::class); }
    public function legalForm(){ return $this->belongsTo(LegalForm::class); }

    // associations for distributors and cinemas
    public function associations(){ return $this->belongsToMany(
        Biz::class,
        $this->sector_slug == 'cinema' ? 'association_cinema' : 'association_distributor',
        $this->sector_slug == 'cinema' ? 'cinema_id' : 'distributor_id', 'association_id')
        ->where('sector_slug', 'association');
    }

    // MorphTo Relations
    public function logo(){ return $this->morphOne(Media::class, 'model')->where('collection_name', 'logo'); }
    public function banners(){ return $this->morphOne(Media::class, 'model')->where('collection_name', 'banner'); }

    public function infos(){ return $this->morphMany(Info::class, 'infoable'); }
    public function addresses(){ return $this->morphMany(Address::class, 'addressable'); }
    public function phones(){ return $this->morphMany(Phone::class, 'phoneable'); }
    public function emails(){ return $this->morphMany(Email::class, 'emailable'); }
    public function websites(){ return $this->morphMany(Website::class, 'websiteable'); }
    public function people(){ return $this->morphMany(Person::class, 'personable'); }
    public function contacts(){ return $this->morphMany(Contact::class, 'contactable'); }
    public function openHours(){ return $this->morphMany(OpenHour::class, 'openhourable'); }
    public function links(){ return $this->morphMany(Link::class, 'linkable'); }




    public function seatsSum(){ return $this->rooms()->sum('seats'); }

    public function scopeSectorName($query){
        $query->join('sectors', 'sector_slug', '=', 'slug')->addSelect(['sectors.name as sector_name']);
    }
    public function scopePrimaryAddress($query, $level = 'manager'){
        $query->leftJoin('addresses', function ($join) {
            $join->on( 'bizs.id', '=', 'addressable_id' )->where('addressable_type', Biz::class)->where('addresses.order', 0);
        })->addSelect(ChatHelper::primaryAddressSelect($level));
    }
    public function scopePrimaryPhone($query){
        $query->leftJoin('phones', function ($join) {
            $join->on( 'bizs.id', '=', 'phoneable_id' )->where('phoneable_type', Biz::class)->where('phones.order', 0);
        })->addSelect(['phone']);
    }
    public function scopePrimaryEmail($query){
        $query->leftJoin('emails', function ($join) {
            $join->on( 'bizs.id', '=', 'emailable_id' )->where('emailable_type', Biz::class)->where('emails.order', 0);
        })->addSelect(['email']);
    }
    public function scopePrimaryWebsite($query){
        $query->leftJoin('websites', function ($join) {
            $join->on( 'bizs.id', '=', 'websiteable_id' )->where('websiteable_type', Biz::class)->where('websites.order', 0);
        })->addSelect(['url']);
    }




//    public function pcDistributor(){
//        return $this->hasOne(PcDistributor::class, 'biz_id');
//    }
//    public function pcCinema(){
//        return $this->hasMany(PcCinema::class, 'biz_id');
//    }
//    public function pcAssociation(){
//        return $this->hasOne(PcCinema::class, 'biz_id');
//    }

}
