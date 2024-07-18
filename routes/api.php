<?php

use App\Events\MessageCreated;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\AuthUserController;
use App\Http\Controllers\BookingServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthUserController::class, 'register'])->name('register');
Route::post('login', [AuthUserController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(
    function () {

        Route::prefix('email_verification')->group(function () {
            Route::get('/send', [EmailVerificationController::class, 'sendVerificationEmail']);
            Route::post('/verify', [EmailVerificationController::class, 'emailVerification']);
        });

        Route::post('logout', [AuthUserController::class, 'logout'])->name('logout');

        Route::get('user_detail/{user_id}', [AuthUserController::class, 'getEmailUser']);

        Route::prefix('admin')->group(function () {
            Route::prefix('manage_user')->group(function () {
                Route::get('/', [ManageUserController::class, 'index']);
                Route::get('/{id}', [ManageUserController::class, 'show']);
                Route::put('/{id}', [ManageUserController::class, 'update']);
                Route::delete('/{id}', [ManageUserController::class, 'destroy']);
            });

            Route::prefix('category')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
                Route::get('/{id}', [CategoryController::class, 'show']);
                Route::post('/', [CategoryController::class, 'store']);
                Route::post('/{id}', [CategoryController::class, 'update']);
                Route::delete('/{id}', [CategoryController::class, 'destroy']);
                Route::get('/search/{name}', [CategoryController::class, 'search']);
            });

            Route::prefix('service')->group(function () {
                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{id}', [ServiceController::class, 'show']);
                Route::post('/', [ServiceController::class, 'store']);
                Route::post('/{id}', [ServiceController::class, 'update']);
                Route::delete('/{id}', [ServiceController::class, 'destroy']);
                Route::get('category/{category_id}', [ServiceController::class, 'serviceByCategory']);
                Route::get('{service_id}/experts', [ServiceController::class, 'fetchExpertsForService']);
            });

            Route::prefix('book_service')->group(function () {
                Route::get('/', [BookingServiceController::class, 'index']);
                Route::get('/{book_id}', [BookingServiceController::class, 'show']);
            });
        });

        Route::prefix('expert')->group(function () {
            Route::prefix('/profile')->group(function () {
                Route::get('/', [ProfileController::class, 'show']);
                Route::post('/', [ProfileController::class, 'update']);
                Route::delete('/', [ProfileController::class, 'destroy']);
            });

            Route::prefix('category')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
                Route::get('/{id}', [CategoryController::class, 'show']);
                Route::get('/search/{name}', [CategoryController::class, 'search']);
            });

            Route::prefix('service')->group(function () {
                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{id}', [ServiceController::class, 'show']);
                Route::get('category/{category_id}', [ServiceController::class, 'serviceByCategory']);
            });

            Route::prefix('book_service')->group(function () {
                Route::get('/', [BookingServiceController::class, 'index']);
                Route::get('/{book_id}', [BookingServiceController::class, 'show']);
                Route::put('/{book_id}', [BookingServiceController::class, 'update']);
                Route::delete('/{book_id}', [BookingServiceController::class, 'destroy']);
            });

            Route::prefix('payment')->group(function () {
                Route::resource('/', PaymentController::class);
            });
        });

        Route::prefix('customer')->group(function () {
            Route::prefix('/profile')->group(function () {
                Route::get('/', [ProfileController::class, 'show']);
                Route::post('/', [ProfileController::class, 'update']);
                Route::delete('/', [ProfileController::class, 'destroy']);
            });

            Route::prefix('service')->group(function () {
                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{id}', [ServiceController::class, 'show']);
                Route::post('{service_id}/book_service', [BookingServiceController::class, 'store']);
                Route::get('category/{category_id}', [ServiceController::class, 'serviceByCategory']);
            });

            Route::prefix('book_service')->group(function () {
                Route::get('/', [BookingServiceController::class, 'index']);
                Route::get('/{book_id}', [BookingServiceController::class, 'show']);
                Route::delete('/{book_id}', [BookingServiceController::class, 'destroy']);
                Route::get('freelancebyservices/{city}', [BookingServiceController::class, 'showFreelancers']);
            });

            Route::prefix('category')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
            });

            Route::prefix('payment')->group(function () {
                Route::get('/', [PaymentController::class, 'index']);
                Route::post('/', [PaymentController::class, 'store']);
            });
        });
    }
);
