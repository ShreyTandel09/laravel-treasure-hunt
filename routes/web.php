<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [GameController::class, 'index'])->name('game.index');
Route::post('/initialize-grid', [GameController::class, 'initializeGrid'])->name('game.initialize');
Route::post('/process-click', [GameController::class, 'processClick'])->name('game.click');
Route::get('/treasure-hunt/{randomNumber}', [GameController::class, 'show'])->name('game.show');
