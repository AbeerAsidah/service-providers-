<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Info extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'super_key',
        'key',
        'value',
    ];
    protected $translatable = ['value'];

    protected function asJson($value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
