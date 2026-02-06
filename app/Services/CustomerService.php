<?php

namespace App\Services;

use App\Interfaces\CustomerInterface;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerService implements CustomerInterface
{
    public function create(Request $request): Customer
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3',
            'is_active' => 'boolean'
        ]);

        return Customer::create($validated);
    }

    public function update(Request $request, Customer $customer): bool
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3',
            'is_active' => 'boolean'
        ]);

        return $customer->update($validated);
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }
}
