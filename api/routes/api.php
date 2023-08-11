<?php

use App\Http\Controllers\CustomersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultantsController;
use App\Http\Controllers\InvoiceController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'consultants'], function () {
    Route::get('', [ConsultantsController::class, 'showConsultants']);
    Route::get('net-revenue', [ConsultantsController::class, 'showNetRevenue']);
});

Route::group(['prefix' => 'customers'], function () {
    Route::get('', [CustomersController::class, 'showCustomers']);
    Route::get('net-revenue', [CustomersController::class, 'showNetRevenue']);
});

Route::group(['prefix' => 'invoices'], function () {
    Route::get('order-dates', [InvoiceController::class, 'getUniqueOrderDates']);
});


