<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // Import this at the top with other imports

class ProductController extends Controller
{
    // Function to display all products
    public function allProducts(Request $request)
    {
        try {
            // Fetch all products, only selecting specific fields
            $products = Product::select('ProductID', 'Name', 'description', 'Price', 'image','Category')->get();
    
            // Return the products as a JSON response with a 200 status code
            return response()->json($products, 200);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Failed to fetch products: ' . $e->getMessage());
    
            // Return a JSON response with an error message and a 500 status code
            return response()->json(['error' => 'Failed to fetch products'], 500);
        }
    }
    // Function to get products Cosmétique
    public function getCosmetiqueProducts()
    {
        // Log the query
        \Log::info('Fetching products for category: Cosmétique');
    
        // Fetch products
        $cosmetiqueProducts = Product::where('Category', 'Cosmétique')->get();
    
        // Check if products were found
        if ($cosmetiqueProducts->isEmpty()) {
            \Log::info('No products found for category: Cosmétique');
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Log the found products
        \Log::info('Found products:', $cosmetiqueProducts->toArray());
    
        return response()->json($cosmetiqueProducts);
    }

    
    // Function to get products Produit alimentaire
    public function getCosmetiqueProduitsAlimentaire()
    {
            // Fetch products with the category "Cosmétique"
            $cosmetiqueProducts = Product::where('Category', 'Produit alimentaire')->get();
    
            // Return a response (can be JSON, view, etc.)
            return response()->json($cosmetiqueProducts);
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
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation rules
    ]);

    // If validation fails, return a 422 response with the validation errors
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Handle image upload if an image is provided
    $imagePath = null;
    if ($request->hasFile('image')) {
        // Store the image in the 'public/products' directory and save the path
        $imagePath = $request->file('image')->store('products', 'public');
    }

    // Create a new product instance and fill it with the validated data
    $product = new Product();
    $product->Name = $request->input('Name');
    $product->Description = $request->input('Description');
    $product->Price = $request->input('Price');
    $product->StockQuantity = $request->input('StockQuantity');
    $product->Category = $request->input('Category');
    $product->image = $imagePath; // Store the image path

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
     public function getProductsByCategory($productId)
     {
         // Retrieve the product by ID
         $product = Product::find($productId);
     
         // Check if the product exists
         if (!$product) {
             return response()->json([
                 'message' => 'Product not found',
             ], 404);
         }
     
         // Fetch products in the same category
         $sameCategoryProducts = Product::where('Category', $product->Category)
                                        ->where('ProductID', '!=', $product->ProductID) // Exclude the current product
                                        ->get();
     
         // Check if there are products in the same category
         if ($sameCategoryProducts->isEmpty()) {
             return response()->json([
                 'message' => 'No products found in the same category',
             ], 404);
         }
     
         // Return the products in the same category
         return response()->json([
             'category' => $product->Category, // Assuming the product belongs to a category
             'products' => $sameCategoryProducts,
         ], 200);
     }
}

     