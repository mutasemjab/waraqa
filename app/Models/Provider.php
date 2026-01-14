<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Add all photo URL attributes to the appends array
    protected $appends = [
        'photo_url',
        'name',
        'email',
        'phone',
        'photo',
        'fcm_token',
        'activate',
        'country_id',
    ];



    /**
     * Helper method to generate image URLs
     *
     * @param string|null $imageName
     * @return string|null
     */
    protected function getImageUrl($imageName)
    {
        if ($imageName) {
            $baseUrl = rtrim(config('app.url'), '/');
            return $baseUrl . '/assets/admin/uploads/' . $imageName;
        }

        return null;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->user ? $this->user->photo_url : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        return $this->user?->name;
    }

    public function getEmailAttribute()
    {
        return $this->user?->email;
    }

    public function getPhoneAttribute()
    {
        return $this->user?->phone;
    }

    public function getPhotoAttribute()
    {
        return $this->user?->photo;
    }

    public function getFcmTokenAttribute()
    {
        return $this->user?->fcm_token;
    }

    public function getActivateAttribute()
    {
        return $this->user?->activate;
    }

    public function getCountryIdAttribute()
    {
        return $this->user?->country_id;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class);
    }

    public function bookRequestResponses()
    {
        return $this->hasMany(BookRequestResponse::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
