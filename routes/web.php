<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RentalContractController;
use App\Http\Controllers\LoanContractController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () { 
    return redirect('/login'); 
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/backup', [DashboardController::class, 'backup'])->name('backup');
    Route::post('/restore', [DashboardController::class, 'restore'])->name('restore');
    
    // Resource Routes
    Route::resource('customers', CustomerController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('rentals', RentalContractController::class);
    Route::get('/rentals/{rental}/print', [RentalContractController::class, 'print'])->name('rentals.print');
    Route::resource('loans', LoanContractController::class);

    // Finance
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/create', [FinanceController::class, 'create'])->name('finance.create');
    Route::post('/finance/search', [FinanceController::class, 'search'])->name('finance.search');
    Route::get('/finance/search-invoices', [FinanceController::class, 'searchInvoices'])->name('finance.searchInvoices');
    Route::get('/finance/select-invoice/{invoice}', [FinanceController::class, 'selectInvoice'])->name('finance.selectInvoice');
    Route::post('/finance', [FinanceController::class, 'store'])->name('finance.store');
    Route::get('/finance/{id}/receipt', [FinanceController::class, 'receipt'])->name('finance.receipt');
    Route::get('/finance/revenue', [FinanceController::class, 'revenue'])->name('finance.revenue');
    Route::delete('/finance/{transaction}', [FinanceController::class, 'destroy'])->name('finance.destroy');
    
    // Reduce Principal
    Route::get('/finance/reduce-principal', [FinanceController::class, 'reducePrincipalForm'])->name('finance.reducePrincipal');
    Route::get('/finance/reduce-principal/contracts', [FinanceController::class, 'getActiveLoans'])->name('finance.getActiveLoans');
    Route::post('/finance/reduce-principal', [FinanceController::class, 'reducePrincipal'])->name('finance.storePrincipal');

    // Invoice
    Route::resource('invoice', InvoiceController::class);
    Route::get('/invoice/{invoice}/payment', [InvoiceController::class, 'payment'])->name('invoice.payment');
    Route::post('/invoice/{invoice}/payment', [InvoiceController::class, 'recordPayment'])->name('invoice.recordPayment');
    Route::get('/invoice/{invoice}/payment-history', [InvoiceController::class, 'paymentHistory'])->name('invoice.paymentHistory');
});

Route::auth(['register' => false]);
