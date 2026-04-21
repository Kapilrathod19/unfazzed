<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceWhatsIncluded extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_whats_includeds';

    protected $fillable = [
        'service_id',
        'title',
    ];

    protected $casts = [
        'service_id' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id')->withTrashed();
    }
}
