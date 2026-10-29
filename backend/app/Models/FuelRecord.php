<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelRecord extends Model
{
    protected $fillable = [
        'date',
        'mileage',
        'liters',
        'cost',
        'notes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'mileage' => 'integer',
        'liters' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
