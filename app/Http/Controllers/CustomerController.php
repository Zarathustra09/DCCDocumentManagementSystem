<?php

namespace App\Http\Controllers;

use App\DataTables\CustomersDataTable;
use App\Models\Customer;
use App\Interfaces\CustomerInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    private CustomerInterface $customerService;

    public function __construct(CustomerInterface $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(CustomersDataTable $dataTable)
    {
        // authorize via policy
        $this->authorize('viewCustomer', Customer::class);

        try {
            return $dataTable->render('customer.index');
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load customers.');
        }
    }

    public function store(Request $request)
    {
        // authorize via policy (class-level)
        $this->authorize('createCustomer', Customer::class);

        try {
            // delegate creation to service
            $this->customerService->create($request);

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
        // authorize via policy (instance-level)
        $this->authorize('editCustomer', $customer);

        try {
            // delegate update to service
            $this->customerService->update($request, $customer);

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
        // authorize via policy (instance-level)
        $this->authorize('deleteCustomer', $customer);

        try {
            // delegate delete to service
            $this->customerService->delete($customer);
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
