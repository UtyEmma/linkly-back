<?php

use App\Http\Controllers\Links\ClickController;
use App\Http\Controllers\Links\LinksController;
use App\Http\Controllers\Pages\AnalyticsController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\SessionController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('guest')->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::prefix('/password')->group(function(){
        Route::post('/recover', [AuthController::class, 'recoverPassword']);
        Route::post('/reset', [AuthController::class, 'resetPassword']);
    });

    Route::prefix('/user')->group(function(){
        Route::get('/remember', [AuthController::class, 'rememberUser']);
    });
    
    Route::post('/page/{page_id}', [PageController::class, 'show']);
    Route::post('/click/{page_id}', [ClickController::class, 'create']);
    Route::post('/session/{page_id}', [SessionController::class, 'create']);

});

Route::middleware('auth:sanctum')->group(function(){
    
    Route::resource('user', UserController::class)->only(['index', 'update', 'destroy']);

    Route::prefix('pages')->group(function(){
        Route::post('/', [PageController::class, 'create']);
        Route::get('/', [PageController::class, 'list']);
        Route::get('/{slug}', [PageController::class, 'details']);
        Route::delete('/{page_id}', [PageController::class, 'destroy']);
        Route::put('/{page_id}', [PageController::class, 'update']);
        Route::get('/{page_id}/stats', [AnalyticsController::class, 'activity']);
    });

    Route::prefix('links/{page}')->group(function(){
        Route::post('/', [LinksController::class, 'store']);
        Route::get('/', [LinksController::class, 'index']);
        Route::put('/reorder', [LinksController::class, 'reorder']);
        Route::get('/shorten', [LinksController::class, 'shorten']);
        Route::put('/{link_id}', [LinksController::class, 'update']);
        Route::delete('/{link_id}', [LinksController::class, 'destroy']);
        Route::get('/{link_id}', [LinksController::class, 'show']);
    });
});