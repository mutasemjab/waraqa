<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Append the photo_url attribute to JSON responses
    protected $appends = ['photo_url'];

    // Add a custom accessor for the photo URL
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            // Use the APP_URL from the .env file
            $baseUrl = rtrim(config('app.url'), '/');
            return $baseUrl . '/assets/admin/uploads/' . $this->photo;
        }

        return null;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function userDepts()
    {
        return $this->hasMany(UserDept::class);
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // Provider profile relationship
    public function provider()
    {
        return $this->hasOne(Provider::class, 'user_id');
    }

    // Provider-related relationships
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function bookRequests()
    {
        return $this->hasMany(BookRequest::class, 'user_id');
    }

    public function bookRequestResponses()
    {
        return $this->hasMany(BookRequestResponse::class, 'user_id');
    }

    // Scope for filtering users without any roles (sellers/regular users)
    public function scopeWithoutRoles($query)
    {
        return $query->whereDoesntHave('roles');
    }

    // Scope for filtering users with customer role
    public function scopeWithCustomerRole($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'customer');
        });
    }

    // Helper methods for role checking
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isProvider()
    {
        return $this->hasRole('provider');
    }

    // Scopes
    public function scopeWhereIsAdmin($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        });
    }

    public function scopeWhereIsProvider($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'provider');
        });
    }
}
