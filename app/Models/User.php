<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_name',
        'logo',
        'contact_person_name',
        'mobile_number',
        'address',
        'exchange_rate',
        'lowest_shipping_charge_per_kg',
        'company_name',
        'admin_logo',
        'address_line1',
        'address_line2',
        'district',
        'country',
        'system_logo',
        'favicon',
        'app_name',
        'apps_home_url',
        'footer_copyright_text',
        'footer_developer_name',
        'footer_developer_link',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'exchange_rate' => 'decimal:4',
            'lowest_shipping_charge_per_kg' => 'decimal:2',
        ];
    }
    
    /**
     * Get the display name (business name or name).
     */
    public function getDisplayNameAttribute()
    {
        return $this->business_name ?? $this->name;
    }
}
