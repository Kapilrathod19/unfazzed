<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\TranslationTrait;

class ServiceOption extends BaseModel
{
    use HasFactory, SoftDeletes;
    use TranslationTrait;

    protected $table = 'service_options';

    protected $fillable = [
        'service_id',
        'name',
        'price',
        'image',
        'status',
        'created_by',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'price' => 'double',
        'status' => 'integer',
        'created_by' => 'integer',
    ];

    public function translations()
    {
        return $this->morphMany(Translations::class, 'translatable');
    }

    public function translate($attribute, $locale = null)
    {
        $locale = $locale ?? app()->getLocale() ?? 'en';
        if ($locale !== 'en') {
            $translation = $this->translations()
                ->where('attribute', $attribute)
                ->where('locale', $locale)
                ->value('value');

            return $translation !== null ? $translation : '';
        }

        return $this->$attribute;
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id')->withTrashed();
    }
}
