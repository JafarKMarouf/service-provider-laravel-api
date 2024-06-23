<?php

use App\Events\MessageCreated;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\AuthController;
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

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(
    function () {

        Route::prefix('email_verification')->group(function () {
            Route::get('/send', [EmailVerificationController::class, 'sendVerificationEmail']);
            Route::post('/verify', [EmailVerificationController::class, 'emailVerification']);
        });

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('user_detail/{user_id}', [AuthController::class, 'getEmailUser']);

        Route::prefix('admin')->group(function () {
            Route::prefix('category')->group(function () {
                Route::get('/', [CategoryController::class, 'index']);
                Route::get('/{id}', [CategoryController::class, 'show']);
                Route::post('/', [CategoryController::class, 'store']);
                Route::put('/{id}', [CategoryController::class, 'update']);
                Route::delete('/{id}', [CategoryController::class, 'destroy']);
                Route::get('/search/{name}', [CategoryController::class, 'search']);
            });

            Route::prefix('manage_user')->group(function () {
                Route::get('/', [ManageUserController::class, 'index']);
                Route::get('/{id}', [ManageUserController::class, 'show']);
                Route::put('/{id}', [ManageUserController::class, 'update']);
                Route::delete('/{id}', [ManageUserController::class, 'destroy']);
            });
            Route::get('/book_service', [BookingServiceController::class, 'index']);
        });

        Route::prefix('expert')->group(function () {
            Route::prefix('/profile')->group(function () {
                Route::get('/{user_id}', [ProfileController::class, 'show']);
                Route::post('/{user_id}', [ProfileController::class, 'update']);
            });

            Route::prefix('service')->group(function () {
                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{id}', [ServiceController::class, 'show']);
                Route::post('/', [ServiceController::class, 'store']);
                Route::post('/{id}', [ServiceController::class, 'update']);
            });

            Route::get('book_service', [BookingServiceController::class, 'index']);
            Route::get('book_service/{book_id}', [BookingServiceController::class, 'show']);
            Route::put('book_service/{book_id}', [BookingServiceController::class, 'update']);

            Route::prefix('payment')->group(function () {
                Route::resource('/', PaymentController::class);
            });
        });

        Route::prefix('customer')->group(function () {
            Route::prefix('/profile')->group(function () {
                Route::get('/{user_id}', [ProfileController::class, 'show']);
                Route::post('/{user_id}', [ProfileController::class, 'update']);
                Route::delete('/{user_id}', [ProfileController::class, 'destroy']);
            });

            Route::prefix('service')->group(function () {
                Route::get('/', [ServiceController::class, 'index']);
                Route::get('/{id}', [ServiceController::class, 'show']);
                Route::post('{service_id}/book_service', [BookingServiceController::class, 'store']);
            });

            Route::prefix('book_service')->group(function () {
                Route::get('/', [BookingServiceController::class, 'index']);
                Route::get('/{book_id}', [BookingServiceController::class, 'show']);
                Route::delete('/{book_id}', [BookingServiceController::class, 'destroy']);
                Route::get('freelancebyservices', [BookingServiceController::class, 'showFreelancers']);
            });

            Route::prefix('payment')->group(function () {
                Route::get('/', [PaymentController::class, 'index']);
                Route::post('/', [PaymentController::class, 'store']);
            });
        });
    }
);
