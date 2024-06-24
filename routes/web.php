<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserDetailsController;

Route::get('/', function () {
    return view('welcome');
});

// SignUp routes
Route::get('/signup', function () {
    return view('signup');
})->name('signup.form');

Route::post('/signup/mobile', [SignupController::class, 'submitMobile'])->name('signup.submitMobile');
Route::post('/signup/otp', [SignupController::class, 'submitOTP'])->name('signup.submitOTP');
Route::post('/signup/name', [SignupController::class, 'submitName'])->name('signup.submitName');

// Login routes
Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login/submitMobile', [UserLoginController::class, 'submitMobile'])->name('userlogin.submitMobile');
Route::post('/login/submitOTP', [UserLoginController::class, 'submitOTP'])->name('userlogin.submitOTP');

// User Details routes
Route::get('/userdetails', [UserDetailsController::class, 'index'])->name('userdetails');
Route::post('/userdetails/store', [UserDetailsController::class, 'store'])->name('userdetails.store');
Route::post('/userdetails/update/{id}', [UserDetailsController::class, 'update'])->name('userdetails.update');
Route::post('/userdetails/delete/{id}', [UserDetailsController::class, 'destroy'])->name('userdetails.delete');



