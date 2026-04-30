<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'type',
        'value',
        'status',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }
}
