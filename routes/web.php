<?php

use Illuminate\Support\Facades\Route;

Route::view('/{any?}', 'root')->where('any', '^(?!api/).*');
