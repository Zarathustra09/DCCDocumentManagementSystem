<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewCustomer(User $user): bool
    {
        return $user->can('view customer');
    }

    public function createCustomer(User $user): bool
    {
        return $user->can('create customer');
    }

    public function editCustomer(User $user, Customer $customer): bool
    {
        return $user->can('edit customer');
    }

    public function deleteCustomer(User $user, Customer $customer): bool
    {
        return $user->can('delete customer');
    }
}
