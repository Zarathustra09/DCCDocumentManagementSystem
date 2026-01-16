<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategory;
use App\Models\DocumentRegistrationEntry;

class SubCategory extends Model
{
    // the migration created table "subcategories" (no underscore), so set explicitly
    protected $table = 'subcategories';

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'main_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

    public function documentRegistrationEntries()
    {
        // kept the existing foreign key name (category_id) for compatibility
        return $this->hasMany(DocumentRegistrationEntry::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Prevent deletion if there are related documents
        static::deleting(function ($subcategory) {
            if ($subcategory->documentRegistrationEntries()->exists()) {
                return false;
            }
        });
    }
}
