<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;

class MainCategory extends Model
{
    protected $fillable = [
        'name',
    ];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class, 'main_category_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Prevent deletion if there are subcategories
        static::deleting(function ($mainCategory) {
            if ($mainCategory->subcategories()->exists()) {
                return false;
            }
        });
    }
}
