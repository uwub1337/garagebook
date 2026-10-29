<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'item',
        'date',
        'mileage',
        'cost',
        'next_due_date',
        'next_due_mileage',
        'notes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'next_due_date' => 'date:Y-m-d',
        'mileage' => 'integer',
        'next_due_mileage' => 'integer',
        'cost' => 'decimal:2',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
