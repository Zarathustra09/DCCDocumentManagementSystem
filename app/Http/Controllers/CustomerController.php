<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $this->authorize('view customer');
        try {
            $customers = Customer::orderBy('name')->get();
            return view('customer.index', compact('customers'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load customers.');
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create customer');
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:customers,code',
                'is_active' => 'boolean'
            ]);

            Customer::create($request->all());

            return response()->json(['success' => true, 'message' => 'Customer created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create customer.'], 500);
        }
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('edit customer');
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:customers,code,' . $customer->id,
                'is_active' => 'boolean'
            ]);

            $customer->update($request->all());

            return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update customer.'], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete customer');
        try {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete customer.'], 500);
        }
    }
}
