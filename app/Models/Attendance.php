<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_lat',
        'check_in_long',
        'check_out_lat',
        'check_out_long',
        'check_in_photo',
        'check_out_photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_lat' => 'decimal:7',
            'check_in_long' => 'decimal:7',
            'check_out_lat' => 'decimal:7',
            'check_out_long' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
