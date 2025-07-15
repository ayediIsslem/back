<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdherentController;
use App\Http\Controllers\PointeuseController;
use App\Http\Controllers\SocieteController;
// Routes publiques (auth: pas encore connectés)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (nécessitent un token JWT valide)
Route::middleware(['auth:api'])->group(function () {
    Route::get('/adherents/{id}/photo', [AdherentController::class, 'getPhoto']);

    // Auth user info, logout, refresh token
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);

    // Adherents
    Route::get('/adherents', [AdherentController::class, 'index']);
    Route::get('/adherents/{id}', [AdherentController::class, 'show']);
    Route::post('/adherents', [AdherentController::class, 'store']);
    Route::put('/adherents/{id}', [AdherentController::class, 'update']);
    Route::delete('/adherents/{id}', [AdherentController::class, 'destroy']);
    Route::post('/adherents/{id}/assigner-pointeuse', [AdherentController::class, 'assignerPointeuse']);
    Route::post('/adherents/{id}/desassigner-pointeuse', [AdherentController::class, 'desassignerPointeuse']);
    
    Route::apiResource('adherents', AdherentController::class);
    // Pointeuses
    Route::get('/pointeuses', [PointeuseController::class, 'index']);
    Route::get('/pointeuses/{id}', [PointeuseController::class, 'show']);
    Route::post('/pointeuses', [PointeuseController::class, 'store']);
    Route::put('/pointeuses/{id}', [PointeuseController::class, 'update']);
    Route::delete('/pointeuses/{id}', [PointeuseController::class, 'destroy']);
    Route::post('/pointeuses/{id}/assigner-adherent', [PointeuseController::class, 'assignerAdherent']);
    Route::post('/pointeuses/{id}/desassigner-adherent', [PointeuseController::class, 'desassignerAdherent']);

    //societe
    Route::get('/societe', [SocieteController::class, 'index']);
Route::post('/societe', [SocieteController::class, 'store']);
Route::put('/societe/{id}', [SocieteController::class, 'update']);
Route::delete('/societe/{id}', [SocieteController::class, 'destroy']);
});
