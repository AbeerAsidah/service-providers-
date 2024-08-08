<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Info extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'super_key',
        'key',
        'value',
    ];

    public $translatable = ['value'];
    public static $imageKeys = [
        'overview-image',
    ];
    public static $commaSepratedKeys = [
    ];
    public static $translatableKeys = [
        'hero-description',
        'sections-header',
        'sections-certificates',
        'sections-courses',
        'sections-bachelor',
        'courses-header',
        'overview-description',
        'overview-online_degrees',
        'overview-short_courses',
        'overview-professional_instructors',
        'instructors-header',
        'application-description' ,
        
    ];

    public function value(): Attribute
    {
        return Attribute::make(

            get: function (mixed $value, array $attributes) {

                if (in_array($attributes['key'], static::$commaSepratedKeys)) {
                    return explode(',', $value);
                }
                if (in_array($attributes['super_key'] . '-' . $attributes['key'], static::$translatableKeys)) {
                    return json_decode($value,true);
                }
                return $value;
            },

            set: function (mixed $value, array $attributes) {

                if (in_array($attributes['key'], static::$commaSepratedKeys)) {
                    return implode(',', $value);
                }
                if (in_array($attributes['key'], static::$translatableKeys)) {
                    return json_encode($value, true) ?? $value;
                }
                return $value;
            }
        );
    }
}
