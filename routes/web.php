<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TempMailController;

Route::get('/tempmail', [TempMailController::class, 'index'])->name('tempmail.index');
Route::post('/tempmail/generate', [TempMailController::class, 'generateEmail'])->name('tempmail.generate');
Route::get('/tempmail/check', [TempMailController::class, 'checkEmails'])->name('tempmail.check');
Route::delete('/tempmail/{tempMail}', [TempMailController::class, 'destroy'])->name('tempmail.destroy');