<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Askedio\SoftCascade\Traits\SoftCascadeTrait; 
class Category extends Model
{
    use HasFactory , SoftDeletes, HasTranslations;  
    protected function asJson($value): bool|string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }


    protected $fillable = [
        'name', 'description'
    ];

    public $translatable = ['name', 'description'];


    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
