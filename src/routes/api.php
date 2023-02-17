<?php

use Bugloos\LaravelLocalization\Controller\LanguageController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/localization')->group(function () {
    Route::get('/languages', [LanguageController::class, 'languages']);
    Route::get('/flag/{locale}', [LanguageController::class, 'flag']);
});

