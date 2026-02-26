<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'office_lat',
        'office_long',
        'radius_meter',
        'work_start',
        'late_tolerance_minutes',
    ];

    protected function casts(): array
    {
        return [
            'office_lat' => 'decimal:7',
            'office_long' => 'decimal:7',
        ];
    }

    public static function instance(): self
    {
        return self::firstOrCreate([], [
            'office_lat' => -6.2000000,
            'office_long' => 106.8166670,
            'radius_meter' => 75,
            'work_start' => '08:00',
            'late_tolerance_minutes' => 15,
        ]);
    }
}
