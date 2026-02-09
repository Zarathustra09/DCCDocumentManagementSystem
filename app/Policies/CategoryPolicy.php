<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubCategory;
use App\Models\MainCategory;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewCategory(User $user): bool
    {
        return $user->hasPermissionTo('view category');
    }

    public function createCategory(User $user): bool
    {
        return $user->hasPermissionTo('create category');
    }

    public function editCategory(User $user, SubCategory|MainCategory $category): bool
    {
        return $user->hasPermissionTo('edit category');
    }

    public function deleteCategory(User $user, SubCategory|MainCategory $category): bool
    {
        return $user->hasPermissionTo('delete category');
    }
}
