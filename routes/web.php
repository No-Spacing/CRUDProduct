<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use Inertia\Inertia;

use App\Http\Controllers\AdminController;


Route::post('/PostLogin', [AdminController::class, 'PostLogin'])->name('post.login');

Route::post('/register-user', [AdminController::class, 'RegisterUser']);

Route::middleware(['admin'])->group(function () {

    Route::inertia('/login', 'Auth/Login');

    Route::inertia('/register', 'Auth/Register');

    Route::inertia('/dashboard', 'Dashboard');
     
    Route::get('/products', [AdminController::class, 'Products']);
    
    Route::inertia('/create-product', 'CreateProduct');
    Route::post('/post-product', [AdminController::class, 'PostProduct']);

    Route::delete('/delete-product/{id}', [AdminController::class, 'DeleteProduct']);

    Route::put('/update-product', [AdminController::class, 'UpdateProduct']);

    Route::get('/logout', [AdminController::class, 'logout']); 
});

