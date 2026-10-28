<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/locale', function (Request $request) {
    $validated = $request->validate([
        'locale' => ['required', 'in:ar,en'],
    ]);

    $request->session()->put(
        'locale',
        $validated['locale']
    );

    return back();
})->name('locale.switch');