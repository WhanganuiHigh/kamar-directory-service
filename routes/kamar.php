<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HandleKamarPost;

Route::post('/kamar', HandleKamarPost::class)->name('kamar');