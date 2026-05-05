<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingServiceOptionMapping extends Model
{
    use HasFactory;

    protected $table = 'booking_service_option_mappings';

    protected $fillable = [
        'booking_id',
        'service_option_id',
        'name',
        'price',
    ];

    protected $casts = [
        'booking_id' => 'integer',
        'service_option_id' => 'integer',
        'price' => 'double',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function serviceOption()
    {
        return $this->belongsTo(ServiceOption::class, 'service_option_id', 'id');
    }
}
