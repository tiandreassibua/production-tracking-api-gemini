<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Pastikan ini ada
use Spatie\Permission\Traits\HasRoles; // <-- IMPORT TRAIT
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // <-- TAMBAHKAN TRAIT DI SINI

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    // Relasi untuk user marketing
    public function marketingProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'marketing_id');
    }

    // Relasi untuk user studio
    public function studioDesigns(): HasMany
    {
        return $this->hasMany(Design::class, 'studio_id');
    }

    // Relasi untuk user keuangan
    public function financeInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'finance_id');
    }

    // Relasi untuk user PPIC
    public function ppicDeliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'ppic_id');
    }
}
