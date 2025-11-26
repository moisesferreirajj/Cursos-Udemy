<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\RastreamentoController;
use App\Http\Controllers\GerenciarController;

// ============================================
// PÁGINA INICIAL
// ============================================
Route::get('/', function () {
    return view('home');
})->name('home');

// ============================================
// DASHBOARD
// ============================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// ============================================
// RELATÓRIOS
// ============================================
Route::controller(RelatoriosController::class)->group(function () {
    Route::get('/relatorios', 'index')->name('relatorios');
    Route::get('/relatorios/export-pdf', 'exportPdf')->name('relatorios.export.pdf');
    Route::get('/relatorios/export-excel', 'exportExcel')->name('relatorios.export.excel');
});

// ============================================
// RASTREAMENTO
// ============================================
Route::controller(RastreamentoController::class)->group(function () {
    Route::get('/rastreamento', 'index')->name('rastreamento');
});

// ============================================
// GERENCIAR - READERS E TAGS
// ============================================
Route::controller(GerenciarController::class)->prefix('gerenciar')->group(function () {
    
    // === READERS ===
    Route::get('/readers', 'indexReaders')->name('gerenciar.readers.index');
    Route::get('/readers/create', 'createReader')->name('gerenciar.readers.create');
    Route::post('/readers', 'storeReader')->name('gerenciar.readers.store');
    Route::get('/readers/{id}/edit', 'editReader')->name('gerenciar.readers.edit');
    Route::put('/readers/{id}', 'updateReader')->name('gerenciar.readers.update');
    Route::delete('/readers/{id}', 'destroyReader')->name('gerenciar.readers.destroy');
    
    // === TAGS ===
    Route::get('/tags', 'indexTags')->name('gerenciar.tags.index');
    Route::get('/tags/create', 'createTag')->name('gerenciar.tags.create');
    Route::post('/tags', 'storeTag')->name('gerenciar.tags.store');
    Route::get('/tags/{id}/edit', 'editTag')->name('gerenciar.tags.edit');
    Route::put('/tags/{id}', 'updateTag')->name('gerenciar.tags.update');
    Route::delete('/tags/{id}', 'destroyTag')->name('gerenciar.tags.destroy');
});

// ============================================
// API ROUTES
// ============================================
Route::prefix('api')->group(function () {
    Route::controller(DashboardController::class)->prefix('rfid')->group(function () {
        Route::post('/reading', 'storeReading');
        Route::get('/stats', 'getRealtimeStats');
        Route::get('/recent', 'getRecentReadings');
    });
    
    Route::controller(RelatoriosController::class)->prefix('relatorios')->group(function () {
        Route::get('/summary', 'summary');
        Route::get('/compare', 'compare');
    });
    
    Route::controller(RastreamentoController::class)->prefix('rastreamento')->group(function () {
        Route::get('/current-location', 'getCurrentLocation');
        Route::get('/movement-history', 'getMovementHistory');
        Route::get('/track-multiple', 'trackMultiple');
        Route::get('/location-by-ip', 'getLocationByIp');
        Route::get('/active-locations', 'getActiveLocations');
    });
});

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'SmartLOG API está funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});