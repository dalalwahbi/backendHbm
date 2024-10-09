<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomEmail; // Make sure this is imported
class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8', // Don't require confirmation here
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);
    
       
    
            // Generate a custom ID
            $customId = $this->generateCustomId($validatedData['name']);
    
            // Create a new user with validated data
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hash the password
                'role' => 'supplier', 
                'compteVerified' => 'yes', 
                'custom_id' => $customId, 
                'phone' => $validatedData['phone'], 
                'address' => $validatedData['address'],
            ]);
    
            // Generate the verification URL with the correct hash
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->email)]
            );
    
            // Send custom email with verification link
            Mail::to($user->email)->send(new CustomEmail($user, $verificationUrl));
    
            // Return success response
            return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error. Please check your input.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during registration. Please try again.',
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ], 500);
        }
    }
    
    

    

// Function to generate custom ID
    private function generateCustomId($name)
    {
    // Extract the first name and convert it to lowercase
    $firstName = strtolower(explode(' ', $name)[0]); // Get the first name
    $userId = 'hbm' . $firstName . Str::random(5); // Generate a random string of 5 characters

    return $userId;
    }

 // Login supplier or admin
    public function login(Request $request)
    {
    try {
        // Validate the request
        $credentials = $request->validate([
            'identifier' => 'required|string', // Accepts either email or custom ID
            'password' => 'required|string',
        ]);

        // Determine if the identifier is an email or custom ID
        $field = filter_var($credentials['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'custom_id';

        // Attempt to log in using the field determined above
        if (Auth::attempt([$field => $credentials['identifier'], 'password' => $credentials['password']])) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            // Log the successful login attempt
            \Log::info('User logged in successfully', ['user_id' => $user->id]);

            return response()->json(['token' => $token, 'user' => $user], 200);
        } else {
            // Log invalid credentials
            \Log::warning('Invalid login attempt', ['identifier' => $credentials['identifier']]);
            throw ValidationException::withMessages([
                'identifier' => ['Invalid credentials provided.'],
            ]);
        }

    } catch (ValidationException $e) {
        return response()->json(['message' => $e->getMessage()], 422);
    } catch (\Exception $e) {
        // Log the error details
        \Log::error('Login error', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'An error occurred during login. Please try again.'], 500);
    }
}

    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
    // Function to return all users with role 'supplier'
    public function getSuppliers()
        {
            // Fetch all users where the role is 'supplier'
            $suppliers = User::where('role', 'supplier')->get();
    
            // Return the list of suppliers in JSON format
            return response()->json($suppliers);
        }
        
      // Function to get details of a specific supplier by ID
      public function getSupplierDetails($id)
      {
          // Fetch the supplier where the role is 'supplier' and the ID matches
          $supplier = User::where('role', 'supplier')->find($id);
  
          // Check if supplier exists
          if ($supplier) {
              // Return supplier details in JSON format
              return response()->json($supplier);
          } else {
              // If supplier not found, return an error message
              return response()->json(['error' => 'Supplier not found'], 404);
          }
      }
}
