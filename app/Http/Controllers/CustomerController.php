<?php

namespace App\Http\Controllers;

use App\DataTables\CustomersDataTable;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index(CustomersDataTable $dataTable)
    {
        $this->authorize('view customer');
        try {
            return $dataTable->render('customer.index');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load customers.');
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create customer');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                 'code' => 'required|string|max:3',
                'is_active' => 'boolean'
            ]);

            Customer::create($validated);

            return response()->json(['success' => true, 'message' => 'Customer created successfully']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to create customer.'], 500);
        }
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorize('edit customer');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3',
                'is_active' => 'boolean'
            ]);

            $customer->update($validated);

            return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update customer', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);
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
            Log::error('Failed to delete customer', [
                'error' => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to delete customer.'], 500);
        }
    }
}
