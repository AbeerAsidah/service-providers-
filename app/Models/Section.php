<?php

namespace App\Models;

use App\Constants\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'parent_id',
        'type',
        'name',
        'image',
        'description',
        'is_free',
    ];
    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {
            $model->hidden = $model->getHiddenAttributes();
        });

        static::saving(function ($model) {
            $model->hidden = $model->getHiddenAttributes();
        });
    }
    public function getHiddenAttributes(): array
    {
        $sectionAttributes = Constants::SECTIONS_TYPES[$this->type]['attributes'];
        $sectionAttributes[] = 'id';
        $allAttributes = Schema::getColumnListing($this->getTable());
        return array_diff($allAttributes, $sectionAttributes);
    }


    public function subSections()
    {
        return $this->hasMany(Section::class, 'parent_id');
    }
}
