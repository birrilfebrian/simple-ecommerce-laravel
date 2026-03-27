<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TopupController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\PaymentController;

use App\Models\Topup;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Client

Route::controller(ClientController::class)->group(function () {
    Route::get('/', 'index')->name('clientHome');
    Route::get('/products', 'products')->name('clientProducts');
    Route::get('/products-search', 'searchProduct')->name('clientProductSearch');
    Route::get('/category', 'category')->name('clientCategory');
    Route::get('/category/{category}', 'categoryProducts')->name('clientCategoryProducts');
    Route::get('/product/{product}', 'productDetail')->name('clientProductDetail');
    Route::get('/about', 'about')->name('clientAbout');
});



Auth::routes();
Route::middleware(['auth'])->group(function () {

    Route::controller(CartController::class)->group(function () {
        Route::get('/carts', 'carts')->name('clientCarts');
        Route::post('/add-to-cart', 'addToCart')->name('clientAddToCart');
        Route::post('/update-cart', 'updateCart')->name('clientUpdateCart');
        Route::post('/delete-cart', 'deleteCart')->name('clientDeleteCart');
    });


    Route::controller(ClientController::class)->group(function () {
        Route::match(['get', 'post'], '/check-order-status', [ClientController::class, 'checkOrderStatus'])->name('clientCheckOrderStatus');
        Route::get('/checkout', 'checkout')->name('clientCheckout');
        Route::post('/checkout-save', 'checkoutSave')->name('clientCheckoutSave');
        Route::get('/check-order', 'checkOrder')->name('clientCheckOrder');
        Route::get('/success/{order_code}', 'successOrder')->name('clientOrderCode');
        Route::post('/upload-amandement/{order_code}', 'uploadAmandement')->name('clientUploadAmandement');
        Route::post('/upload-temporary', 'uploadTemporary')->name('upload.temporary');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::get('/topup', 'index')->name('topup.index');          // Halaman pilih paket credit
        Route::post('/topup/store', 'store')->name('topup.store');    // Proses simpan permintaan topup
        Route::get('/topup/history', 'history')->name('topup.history'); // Riwayat topup user
        Route::get('/topup/pay/{reference_code}', 'pay')->name('topup.pay'); // Halaman instruksi bayar & upload bukti
        Route::post('/topup/upload-proof', 'uploadProof')->name('topup.uploadProof'); // Proses upload bukti transfer
    });
});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Shop
    Route::controller(ShopController::class)->group(function () {
        Route::post('/shop/create', 'create')->name('shopCreate');
        Route::get('/shop/detail', 'detail')->name('shopDetail');
        Route::post('/shop/update', 'update')->name('shopUpdate');
        Route::post('/shop/update-password', 'updatePassword')->name('shopUpdatePassword');
    });

    // Category
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/admin/category', 'index')->name('category');
        Route::get('/admin/category/create', 'create')->name('categoryCreate');
        Route::post('/admin/category/check', 'check')->name('categoryCheck');
        Route::post('/admin/category/save', 'save')->name('categorySave');
        Route::get('/admin/category/delete/{id}/{path}', 'delete')->name('categoryDelete');
    });

    // Product
    Route::controller(ProductController::class)->group(function () {
        Route::get('/admin/products', 'index')->name('products');
        Route::get('/admin/product/create', 'create')->name('productCreate');
        Route::post('/admin/product/check', 'check')->name('productCheck');
        Route::post('/admin/product/save', 'save')->name('producSave');
        Route::post('/admin/product/images/', 'getImages')->name('productGetImages');
        Route::get('/admin/product/images/{product}', 'addImages')->name('productAddImages');
        Route::post('/admin/product/images/save', 'addImagesSave')->name('productAddImagesSave');
        Route::post('/admin/product/images/delete', 'deleteImages')->name('productDeleteImages');
        Route::get('/admin/product/edit/{product}', 'edit')->name('productEdit');
        Route::post('/admin/product/edit/{product}/{id}/save', 'editSave')->name('productEditSave');
        Route::get('/admin/product/delete/{id}', 'delete')->name('productDelete');
    });

    // Orders
    Route::controller(OrderController::class)->group(function () {
        Route::get('/admin/orders', 'index')->name('orders');
        Route::get('/admin/order/{order_code}', 'detail')->name('orderDetail');
        Route::post('/admin/order/update-status/{order_code}', 'updateStatus')->name('orderUpdateStatus');
        Route::get('/admin/order/delete/{order_code}', 'delete')->name('orderDelete');
    });

    //Topups
    Route::controller(TopupController::class)->group(function () {
        Route::get('/admin/topups', 'index')->name('adminTopupIndex'); // List topup masuk
        Route::get('/admin/topup/{ref_code}', 'detail')->name('adminTopupDetail');
        Route::post('/admin/topup/{ref_code}/approve', 'approveTopup')->name('adminTopupApprove'); // Approve saldo
        Route::post('/admin/topup/{ref_code}/reject', 'rejectTopup')->name('adminTopupReject');   // Tolak topup
    });
});
