<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/checkout', [OrderController::class, 'checkout']);
Route::post('/callback', [OrderController::class, 'callback']);
Route::get('/test', [PaymentController::class, 'test']);
Route::get('/order-chart', [OrderController::class, 'orderChart']);
// Route::get('/assign-address',[AddressController::class, 'assignProvinceToDB']);

Route::get('/provinces',[ProvinceController::class, 'index']);
Route::get('/cities',[CityController::class, 'index']);
Route::post('/cities-by-province',[CityController::class, 'provinceCities']);

Route::group(['middleware'=>['role:admin']], function(){
    Route::resource('products', ProductController::class)->except('index', 'show', 'store');
    Route::resource('users', UserController::class)->except('show');
    Route::get('user-addresses/{id}', [AddressController::class, 'showUserAddresses']);
    Route::get('user-profile/{id}', [ProfilesController::class, 'showUserProfile']);
    Route::put('user-profiles/{id}', [UserController::class, 'updateUserInformation']);
});


Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::apiResource('profile', ProfilesController::class);
    
    Route::apiResource('address', AddressController::class);
    Route::put('set-main-address/{id}', [AddressController::class ,'setMainAddress']);

    Route::apiResource('cart', CartController::class);
    Route::put('/cart/all/{id}', [CartController::class, 'selectingItem']);
    Route::post('/cart-to-wishlist', [CartController::class, 'moveItemToWishlist']);
    
    Route::post('/wishlist-to-cart', [WishlistController::class, 'moveItemToCart']);
    Route::apiResource('wishlist', WishlistController::class);
    
    Route::apiResource('order', OrderController::class);
    Route::get('/order-user', [OrderController::class, 'showUserOrder']);
    
    Route::get('payment-method-list', [PaymentController::class, 'getPaymentMethod']);
    Route::post('/checkout', [PaymentController::class, 'checkout']);
    Route::post('/callback', [PaymentController::class, 'callback']);
    Route::get('/check-status', [PaymentController::class, 'checkStatus']);
    
    Route::get('/invoice-user/{id}', [InvoicesController::class, 'showAllUserInvoices']);
    Route::apiResource('inv',InvoicesController::class);
});


Route::apiResource('courier', CourierController::class);

Route::get('/products',[ProductController::class, 'index']);

Route::get('/roles-and-permissions',[RolePermissionController::class, 'index'])->middleware(['auth:sanctum']);

Route::get('/search', [ProductController::class, 'search']);

Route::get('/users/{id}', [UserController::class, 'show']);

Route::put('products/update-displayed/{productId}', [ProductController::class, 'updateDisplayed']);

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'code' => Response::HTTP_NOT_ACCEPTABLE,
        'status' => Response::$statusTexts[Response::HTTP_NOT_ACCEPTABLE],
        'message' => [
            'error' => 'Sumber tidak ditemukan' // Adjust this message as needed
        ]
    ], Response::HTTP_NOT_ACCEPTABLE);
});
