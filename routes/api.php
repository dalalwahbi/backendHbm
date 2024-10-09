<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerInfoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;



// User Registration and Login
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Forgot password request
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset route
Route::post('/password/reset', [ResetPasswordController::class, 'reset']);

Route::get('password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

// Email Verification Routes
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');


Route::post('email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth'])
    ->name('verification.resend');

//product controller
Route::get('/products', [ProductController::class, 'allProducts']); // To get all products
Route::get('/products/{id}', [ProductController::class, 'showProduct']); // To get a single product by ID
Route::post('/products', [ProductController::class, 'createProduct']); // To create a new product
Route::post('/products/{id}/add-to-cart', [ProductController::class, 'addToCart']);

//create a customer
Route::middleware('auth:sanctum')->post('/customers', [CustomerInfoController::class, 'createCustomer']);

//CREATE ORDER 
Route::middleware('auth:sanctum')->post('/orders/create', [OrderController::class, 'createOrder']);
// Route::post('/orders/create', [OrderController::class, 'createOrder']);

Route::middleware('auth:sanctum')->get('/get-orders', [OrderController::class, 'getOrders']);
Route::middleware('auth:sanctum')->get('/orders/{orderId}', [OrderController::class, 'getOrder']);

// Route for fetching all users with the role 'supplier'
Route::middleware('auth:sanctum')->get('/suppliers', [UserController::class, 'getSuppliers']);
Route::get('/suppliers/{id}', [UserController::class, 'getSupplierDetails']);
// Route for fetching the details of a specific customer
Route::get('/customers/{id}', [CustomerInfoController::class, 'getCustomerDetails']);
// Route for fetching all customers of a specific supplier
Route::get('/suppliers/{supplierId}/customers', [CustomerInfoController::class, 'getCustomersBySupplier']);
// Route for deleting a product
Route::delete('/products/{productId}', [ProductController::class, 'deleteProduct']);

// Route for updating a product
Route::put('/products/{productId}', [ProductController::class, 'updateProduct']);

// Route to calculate the total of products in an order
Route::get('/orders/{orderId}/total', [OrderController::class, 'calculateOrderTotal']);
//update an order
Route::middleware('auth:sanctum')->put('/orders/{orderId}', [OrderController::class, 'updateOrder']);

//delete an order
Route::middleware('auth:sanctum')->delete('/orders/{orderId}', [OrderController::class, 'deleteOrder']);

//update payement status 
Route::put('/orders/{orderId}/payment-status', [PaymentController::class, 'updatePaymentStatus']);
