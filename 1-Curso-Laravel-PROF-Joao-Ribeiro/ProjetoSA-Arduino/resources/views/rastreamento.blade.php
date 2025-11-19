@extends('layouts.app')

@section('title', 'Rastreamento - SmartLOG')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush

@section('toolbar')
    <div class="toolbar py-5 py-lg-15" id="kt_toolbar" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);">
        <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
            <div class="page-title d-flex flex-column me-3">
                <h1 class="d-flex text-white fw-bolder my-1 fs-3">Rastreamento em Tempo Real</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-white opacity-75">
                        <a href="{{ url('/') }}" class="text-white text-hover-primary">SmartLOG</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-white opacity-75">Rastreamento</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        
        <!-- Busca Rápida -->
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-search text-primary fs-2"></i>
                    <span class="ms-2">Localizar Tag</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('rastreamento') }}" method="GET">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <label class="form-label fw-bold">Selecione uma Tag para Rastrear:</label>
                            <select name="tag_id" class="form-select form-select-solid form-select-lg" required>
                                <option value="">-- Selecione uma Tag --</option>
                                @foreach($allTags as $tag)
                                    <option value="{{ $tag->tag_id }}" {{ request('tag_id') == $tag->tag_id ? 'selected' : '' }}>
                                        {{ $tag->tag_id }} - {{ $tag->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-geo-alt-fill"></i> Rastrear Agora
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($selectedTag) && $lastReading)
        <!-- Localização Atual -->
        <div class="row g-5 mb-5">
            <div class="col-lg-12">
                <div class="card card-flush" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);">
                    <div class="card-body p-8 text-center">
                        <i class="bi bi-pin-map-fill text-white" style="font-size: 4rem;"></i>
                        <h2 class="text-white fw-bolder mt-4 mb-2">Localização Atual</h2>
                        <div class="fs-1 fw-bold text-white mb-4">
                            {{ $lastReading->location }}
                        </div>
                        <div class="d-flex justify-content-center gap-4 flex-wrap">
                            <div class="bg-white bg-opacity-25 rounded px-6 py-3">
                                <div class="text-white fs-7">Última Leitura</div>
                                <div class="text-white fs-5 fw-bold">{{ $lastReading->read_at->format('d/m/Y H:i:s') }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded px-6 py-3">
                                <div class="text-white fs-7">Tempo Decorrido</div>
                                <div class="text-white fs-5 fw-bold">{{ $lastReading->read_at->diffForHumans() }}</div>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded px-6 py-3">
                                <div class="text-white fs-7">Status</div>
                                <div class="text-white fs-5 fw-bold text-capitalize">{{ $lastReading->status }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Produto e Leitor -->
        <div class="row g-5 mb-5">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">📦 Informações do Produto</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="bi bi-tag-fill text-primary fs-2x"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted fs-8">Tag ID</div>
                                <div class="fs-5 fw-bold text-gray-800">{{ $selectedTag->tag_id }}</div>
                            </div>
                        </div>

                        <div class="separator my-4"></div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">Produto:</span>
                            <div class="fs-5 fw-bold text-gray-800">{{ $selectedTag->product_name }}</div>
                        </div>

                        @if($selectedTag->product_code)
                        <div class="mb-4">
                            <span class="text-muted fs-7">Código:</span>
                            <div class="fs-5 fw-bold text-gray-800">{{ $selectedTag->product_code }}</div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <span class="text-muted fs-7">Tipo:</span>
                            <div class="fs-5 fw-bold text-gray-800 text-capitalize">{{ $selectedTag->type }}</div>
                        </div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">Status da Tag:</span>
                            <div>
                                @if($selectedTag->status == 'ativo')
                                    <span class="badge badge-success badge-lg">✓ Ativo</span>
                                @else
                                    <span class="badge badge-danger badge-lg">✗ Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title">🔌 Informações do Leitor</h3>
                    </div>
                    <div class="card-body">
                        @if($reader)
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-success">
                                    <i class="bi bi-broadcast text-success fs-2x"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-muted fs-8">Leitor</div>
                                <div class="fs-5 fw-bold text-gray-800">{{ $reader->name }}</div>
                            </div>
                        </div>

                        <div class="separator my-4"></div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">ID do Leitor:</span>
                            <div class="fs-6 fw-bold text-gray-800">{{ $reader->reader_id }}</div>
                        </div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">Endereço IP:</span>
                            <div class="fs-6 fw-bold text-primary">
                                <i class="bi bi-hdd-network"></i> {{ $reader->ip_address ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">Localização Geográfica:</span>
                            <div class="fs-6 fw-bold text-gray-800">
                                <i class="bi bi-pin-map"></i> {{ $reader->location }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="text-muted fs-7">Status do Leitor:</span>
                            <div>
                                @if($reader->isOnline())
                                    <span class="badge badge-success badge-lg">
                                        <i class="bi bi-wifi"></i> Online
                                    </span>
                                @else
                                    <span class="badge badge-danger badge-lg">
                                        <i class="bi bi-wifi-off"></i> Offline
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($reader->last_ping)
                        <div class="mb-4">
                            <span class="text-muted fs-7">Último Ping:</span>
                            <div class="fs-7 text-muted">{{ $reader->last_ping->format('d/m/Y H:i:s') }}</div>
                        </div>
                        @endif
                        @else
                        <div class="text-center py-10">
                            <i class="bi bi-broadcast-pin fs-3x text-muted"></i>
                            <div class="fs-6 text-muted mt-3">Nenhum leitor detectado</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimas 10 Movimentações -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-clock-history text-primary fs-2"></i>
                    <span class="ms-2">Últimas 10 Movimentações</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-hover align-middle gs-0 gy-3">
                        <thead>
                            <tr class="fs-7 fw-bold text-gray-600 border-bottom border-gray-300">
                                <th class="min-w-150px">Data/Hora</th>
                                <th class="min-w-200px">Localização</th>
                                <th class="min-w-150px">Leitor</th>
                                <th class="min-w-100px">IP</th>
                                <th class="min-w-100px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                            <tr>
                                <td>
                                    <div class="text-gray-800 fw-bold">{{ $movement->read_at->format('d/m/Y') }}</div>
                                    <div class="text-muted fs-8">{{ $movement->read_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <span class="text-gray-800 fw-bold">{{ $movement->location }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">{{ $movement->reader_id }}</span>
                                </td>
                                <td>
                                    @if($movement->reader && $movement->reader->ip_address)
                                        <span class="text-primary">
                                            <i class="bi bi-hdd-network"></i> {{ $movement->reader->ip_address }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movement->status == 'entrada')
                                        <span class="badge badge-success">
                                            <i class="bi bi-arrow-down-circle"></i> Entrada
                                        </span>
                                    @elseif($movement->status == 'saida')
                                        <span class="badge badge-danger">
                                            <i class="bi bi-arrow-up-circle"></i> Saída
                                        </span>
                                    @else
                                        <span class="badge badge-info">
                                            <i class="bi bi-arrow-left-right"></i> Movimentação
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-10">
                                    <i class="bi bi-inbox fs-3x d-block mb-3"></i>
                                    Nenhuma movimentação registrada
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @elseif(request('tag_id'))
        <!-- Tag não encontrada ou sem leituras -->
        <div class="card">
            <div class="card-body text-center py-20">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
                <h3 class="mt-5 mb-3">Nenhuma Leitura Encontrada</h3>
                <p class="text-muted fs-5">
                    A tag selecionada não possui registros de leitura.<br>
                    Aguarde o ESP32 detectar esta tag ou selecione outra.
                </p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('custom-js')
<style>
/* Força exibição dos badges */
.badge {
    display: inline-flex !important;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 0.75rem !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    border-radius: 0.375rem !important;
}

.badge-light-primary {
    background-color: rgba(54, 153, 255, 0.1) !important;
    color: #3699FF !important;
    border: 1px solid rgba(54, 153, 255, 0.3) !important;
}

.badge-success {
    background-color: #50CD89 !important;
    color: white !important;
}

.badge-danger {
    background-color: #F1416C !important;
    color: white !important;
}

.badge-info {
    background-color: #7239EA !important;
    color: white !important;
}

.badge-lg {
    padding: 0.75rem 1rem !important;
    font-size: 1rem !important;
}

/* Garantir que os ícones apareçam */
.bi {
    display: inline-block !important;
    font-family: 'bootstrap-icons' !important;
}

/* Garantir visibilidade */
.badge-success i,
.badge-danger i,
.badge-info i {
    color: white !important;
}

/* Tabela responsiva */
.table td {
    vertical-align: middle !important;
    padding: 1rem !important;
}
</style>

<script>
// Auto-refresh a cada 10 segundos se tiver tag selecionada
@if(isset($selectedTag))
setInterval(function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tag_id')) {
        location.reload();
    }
}, 10000);
@endif
</script>
@endsection