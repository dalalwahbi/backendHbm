<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import this at the top with other imports

class ProductController extends Controller
{
    // Function to display all products
    public function allProducts()
    {
        // Fetch all products, only selecting 'Name' and 'Price'
        $products = Product::select('ProductID', 'Name', 'description','Price')->get();

        // Return the products as a JSON response (or view if using Blade)
        return response()->json($products);
    }

    // Function to display a single product by its ID
    public function showProduct($id)
    {
        // Fetch the product by its ID
        $product = Product::find($id);

        // Check if product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Return the product details as a JSON response
        return response()->json($product);
    }

    // Function to create a new product
    public function createProduct(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'Name' => 'required|string|max:255',
            'Description' => 'nullable|string',
            'Price' => 'required|numeric|min:0',
            'StockQuantity' => 'required|integer|min:0',
            'Category' => 'required|string|max:255',
        ]);

        // If validation fails, return a 422 response with the validation errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create a new product instance and fill it with the validated data
        $product = new Product();
        $product->Name = $request->input('Name');
        $product->Description = $request->input('Description');
        $product->Price = $request->input('Price');
        $product->StockQuantity = $request->input('StockQuantity');
        $product->Category = $request->input('Category');

        // Save the product to the database
        $product->save();

        // Return a success response
        return response()->json(['message' => 'Product created successfully!', 'product' => $product], 201);
    }

    //function addToCart
    public function addToCart(Request $request, $id)
    {
        // Validate the request to ensure quantity is provided
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Check if there is enough stock
        if ($product->StockQuantity < $request->quantity) {
            return response()->json(['message' => 'Not enough stock available.'], 400);
        }

        // Decrement the stock quantity
        $product->StockQuantity -= $request->quantity;
        $product->save();

        return response()->json(['message' => 'Product added to cart successfully!', 'product' => $product]);
    }
    // Function to delete a product
    public function deleteProduct($productId)
    {
            // Find the product by ProductID
            $product = Product::find($productId);
    
            if ($product) {
                // Delete the product
                $product->delete();
    
                // Return success message
                return response()->json(['message' => 'Product deleted successfully']);
            } else {
                // If product is not found, return an error
                return response()->json(['error' => 'Product not found'], 404);
            }
    
    }
     // Function to update product
     public function updateProduct(Request $request, $productId)
     {
         // Find the product by ProductID
         $product = Product::find($productId);
 
         if ($product) {
             // Validate incoming request data
             $validated = $request->validate([
                 'Name' => 'required|string|max:255',
                 'Description' => 'nullable|string',
                 'Price' => 'required|numeric|min:0',
                 'StockQuantity' => 'required|integer|min:0',
                 'Category' => 'required|string|max:255',
             ]);
 
             // Update product details
             $product->update([
                 'Name' => $validated['Name'],
                 'Description' => $validated['Description'],
                 'Price' => $validated['Price'],
                 'StockQuantity' => $validated['StockQuantity'],
                 'Category' => $validated['Category'],
             ]);
 
             // Return success message
             return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
         } else {
             // If product is not found, return an error
             return response()->json(['error' => 'Product not found'], 404);
         }
     }
}

