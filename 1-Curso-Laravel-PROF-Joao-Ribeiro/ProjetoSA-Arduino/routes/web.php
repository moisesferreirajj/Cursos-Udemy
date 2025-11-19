<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelatoriosController;
use App\Http\Controllers\RastreamentoController;

/*
|--------------------------------------------------------------------------
| ROTAS WEB - SmartLOG
|--------------------------------------------------------------------------
| Estrutura organizada seguindo os controllers e views criados
|--------------------------------------------------------------------------
*/

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
    // Página principal de relatórios
    Route::get('/relatorios', 'index')
        ->name('relatorios');
    
    // Exportações
    Route::get('/relatorios/export-pdf', 'exportPdf')
        ->name('relatorios.export.pdf');
    
    Route::get('/relatorios/export-excel', 'exportExcel')
        ->name('relatorios.export.excel');
});

// ============================================
// RASTREAMENTO
// ============================================
Route::controller(RastreamentoController::class)->group(function () {
    // Página principal de rastreamento
    Route::get('/rastreamento', 'index')
        ->name('rastreamento');
});

/*
|--------------------------------------------------------------------------
| API ROUTES - ESP32
|--------------------------------------------------------------------------
| Rotas para comunicação com ESP32 e RFID
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    
    // ============================================
    // API RFID (ESP32)
    // ============================================
    Route::controller(DashboardController::class)->prefix('rfid')->group(function () {
        // Receber leituras do ESP32
        Route::post('/reading', 'storeReading');
        
        // Estatísticas em tempo real
        Route::get('/stats', 'getRealtimeStats');
        
        // Leituras recentes
        Route::get('/recent', 'getRecentReadings');
    });
    
    // ============================================
    // API RELATÓRIOS
    // ============================================
    Route::controller(RelatoriosController::class)->prefix('relatorios')->group(function () {
        // Resumo por período (today, week, month, year)
        Route::get('/summary', 'summary');
        
        // Comparar múltiplas tags
        Route::get('/compare', 'compare');
    });
    
    // ============================================
    // API RASTREAMENTO
    // ============================================
    Route::controller(RastreamentoController::class)->prefix('rastreamento')->group(function () {
        // Localização atual de uma tag
        Route::get('/current-location', 'getCurrentLocation');
        
        // Histórico de movimentações
        Route::get('/movement-history', 'getMovementHistory');
        
        // Rastrear múltiplas tags
        Route::get('/track-multiple', 'trackMultiple');
        
        // Buscar localização por IP do ESP32
        Route::get('/location-by-ip', 'getLocationByIp');
        
        // Todas localizações ativas (para mapa/heatmap)
        Route::get('/active-locations', 'getActiveLocations');
    });
});

/*
|--------------------------------------------------------------------------
| ROTAS AUXILIARES
|--------------------------------------------------------------------------
*/

// Rota de teste (opcional - pode remover em produção)
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'SmartLOG API está funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

/*
|--------------------------------------------------------------------------
| FALLBACK
|--------------------------------------------------------------------------
*/

// Rota 404 personalizada (opcional)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});