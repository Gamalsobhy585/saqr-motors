<?php

use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\LeadController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;

// Apply the existing session locale middleware to every public page.
Route::middleware(SetLocale::class)->group(function (): void {
    Route::get('/', [CatalogController::class, 'home'])->name('home');
    Route::get('/motorcycles', [CatalogController::class, 'index'])->name('motorcycles.index');
    Route::get('/motorcycles/{brand}/{motorcycle}', [CatalogController::class, 'show'])
        ->name('motorcycles.show');

    Route::post('/motorcycles/{motorcycle:uuid}/test-rides', [LeadController::class, 'book'])
        ->middleware('throttle:saqr-test-ride')
        ->name('motorcycles.book');

    Route::post('/motorcycles/{motorcycle:uuid}/contact', [LeadController::class, 'contact'])
        ->middleware('throttle:saqr-contact')
        ->name('motorcycles.contact');

});
