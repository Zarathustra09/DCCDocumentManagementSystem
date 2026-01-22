<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategory;
use App\Models\DocumentRegistrationEntry;

/*
 This model is kept for backward compatibility.
 The categories table was renamed to subcategories; point this model to the new table.
*/
class Category extends Model
{
    // map to the renamed table
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
        return $this->hasMany(DocumentRegistrationEntry::class, 'category_id');
    }
}
