<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Satu Project dimiliki oleh satu Client
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Satu Project ditangani oleh satu user Marketing
    public function marketing(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    // Satu Project memiliki banyak Item
    public function items(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }

    // Satu Project memiliki satu Desain
    public function design(): HasOne
    {
        return $this->hasOne(Design::class);
    }

    // Satu Project memiliki satu Penawaran
    public function quotation(): HasOne
    {
        return $this->hasOne(Quotation::class);
    }

    // Satu Project memiliki satu SPK
    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class);
    }

    // Satu Project memiliki banyak Tagihan
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Satu Project memiliki banyak Pengiriman
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
}
