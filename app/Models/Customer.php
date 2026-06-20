<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasFactory;

    protected $guard = 'customer';

    // 🔥 এখানে সব প্রয়োজনীয় ফিল্ডগুলো রাখলাম
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'email',
        'password',
        'verify',
        'status',
        'forgot',
        'address',
        'district',
        'area',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function cust_area()
    {
        return $this->belongsTo(District::class, 'area');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
