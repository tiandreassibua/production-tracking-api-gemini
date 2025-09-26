<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Seorang Klien bisa memiliki banyak Proyek.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
