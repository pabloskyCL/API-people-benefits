<?php

use App\Http\Controllers\BenefitsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('yearBenefits', [BenefitsController::class, 'yearBenefits']);
