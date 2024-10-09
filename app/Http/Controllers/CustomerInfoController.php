<?php

namespace App\Http\Controllers;

use App\Models\CustomerInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerInfoController extends Controller
{
    // Method to create a new customer
    public function createCustomer(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'Name' => 'required|string|max:255',
            'Email' => 'required|email|unique:customer_infos,Email',
            'Phone' => 'nullable|string|max:20',
            'Address' => 'required|string|max:255',
            'City' => 'required|string|max:255',
        ]);

        // If validation fails, return error message
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the customer
        $customer = CustomerInfo::create([
            'SupplierID' => auth()->user()->id, // Assuming the supplier is the logged-in user
            'Name' => $request->input('Name'),
            'Email' => $request->input('Email'),
            'Phone' => $request->input('Phone'),
            'Address' => $request->input('Address'),
            'City' => $request->input('City'),
        ]);

        // Return success message with the newly created customer details
        return response()->json([
            'message' => 'Customer created successfully!',
            'customer' => $customer,
        ], 201);
    }
    // Function to get details of a specific customer by ID
    public function getCustomerDetails($id)
    {
            // Fetch the customer by the CustomerID
            $customer = CustomerInfo::find($id);
    
            // Check if customer exists
            if ($customer) {
                // Return customer details in JSON format
                return response()->json($customer);
            } else {
                // If customer not found, return an error message
                return response()->json(['error' => 'Customer not found'], 404);
            }
    }
    // Function to get all customers of a specific supplier
    public function getCustomersBySupplier($supplierId)
    {
            // Fetch all customers related to the specified SupplierID
            $customers = CustomerInfo::where('SupplierID', $supplierId)->get();
    
            // Check if any customers exist
            if ($customers->isNotEmpty()) {
                // Return the list of customers in JSON format
                return response()->json($customers);
            } else {
                // If no customers found, return an error message
                return response()->json(['error' => 'No customers found for this supplier'], 404);
            }
    }
}
