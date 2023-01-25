<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\ResetPasswordController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::prefix('')->middleware('auth','isAdmin')->group(function () {
// });



//Practice for Sanctum Authentication
//Public Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send-reset-password-email', [ResetPasswordController::class, 'send_reset_password_email']);
Route::post('/reset-password/{token}', [ResetPasswordController::class, 'reset']);


//Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/loggeduser', [UserController::class, 'logged_user']);
    Route::post('/changepassword', [UserController::class, 'change_password']);
    Route::get('/user', [UserController::class, 'user'])->middleware('role:user');
    Route::get('/admin', [UserController::class, 'admin'])->middleware('role:admin');
});


//Practice Sanctum with Crud Operation
Route::resource('product', ProductController::class);
