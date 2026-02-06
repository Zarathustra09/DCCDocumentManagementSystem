<?php

namespace App\Interfaces;

use App\Models\Customer;
use Illuminate\Http\Request;

interface CustomerInterface
{
    public function create(Request $request): Customer;

    public function update(Request $request, Customer $customer): bool;

    public function delete(Customer $customer): bool;
}
