<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Section extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'type',
        'parent_id',
    ];

    /**
     * Get the parent section.
     */
    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    /**
     * Get the child sections.
     */
    public function children()
    {
        return $this->hasMany(Section::class, 'parent_id');
    }

   

}