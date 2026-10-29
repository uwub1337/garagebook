<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    protected $fillable = [
        'name',
        'make',
        'model',
        'year',
        'plate',
        'current_mileage',
        'fuel_type',
        'photo_path',
    ];

    protected $casts = [
        'year' => 'integer',
        'current_mileage' => 'integer',
    ];

    protected $appends = ['photo_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function fuelRecords(): HasMany
    {
        return $this->hasMany(FuelRecord::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? '/uploads/'.$this->photo_path : null;
    }
}
