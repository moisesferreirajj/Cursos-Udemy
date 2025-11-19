@extends('layouts.app')

@section('title', 'Relatórios - SmartLOG')

@section('toolbar')
    <div class="toolbar py-5 py-lg-15" id="kt_toolbar" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);">
        <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
            <div class="page-title d-flex flex-column me-3">
                <h1 class="d-flex text-white fw-bolder my-1 fs-3">Relatórios RFID</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-white opacity-75">
                        <a href="{{ url('/') }}" class="text-white text-hover-primary">SmartLOG</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-white opacity-75">Relatórios</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        
        <!-- Filtro de Busca -->
        <div class="card mb-5">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-search text-primary fs-2"></i>
                    <span class="ms-2">Buscar Histórico de Tag</span>
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('relatorios') }}" method="GET" id="filterForm">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label fw-bold">Selecione uma Tag:</label>
                            <select name="tag_id" class="form-select form-select-solid" id="tagSelect" required>
                                <option value="">-- Selecione uma Tag --</option>
                                @foreach($allTags as $tag)
                                    <option value="{{ $tag->tag_id }}" {{ request('tag_id') == $tag->tag_id ? 'selected' : '' }}>
                                        {{ $tag->tag_id }} - {{ $tag->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-lg-3">
                            <label class="form-label fw-bold">Data Inicial:</label>
                            <input type="date" name="start_date" class="form-control form-control-solid" 
                                   value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-lg-3">
                            <label class="form-label fw-bold">Data Final:</label>
                            <input type="date" name="end_date" class="form-control form-control-solid" 
                                   value="{{ request('end_date', now()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-lg-2">
                            <label class="form-label fw-bold">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($selectedTag))
        <!-- Informações da Tag -->
        <div class="row g-5 mb-5">
            <div class="col-lg-4">
                <div class="card card-flush h-100">
                    <div class="card-header">
                        <h3 class="card-title">📦 Informações da Tag</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span class="text-muted fs-7">Tag ID:</span>
                            <div class="fs-5 fw-bold text-gray-800">{{ $selectedTag->tag_id }}</div>
                        </div>
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
                            <span class="text-muted fs-7">Status:</span>
                            <div>
                                @if($selectedTag->status == 'ativo')
                                    <span class="badge badge-success fs-7">✓ Ativo</span>
                                @else
                                    <span class="badge badge-danger fs-7">✗ Inativo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-flush h-100">
                    <div class="card-header">
                        <h3 class="card-title">📊 Estatísticas do Período</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="border border-dashed border-primary rounded p-4 text-center">
                                    <div class="fs-2x fw-bold text-primary">{{ $stats['total'] }}</div>
                                    <div class="text-muted fs-7">Total de Leituras</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-dashed border-success rounded p-4 text-center">
                                    <div class="fs-2x fw-bold text-success">{{ $stats['entradas'] }}</div>
                                    <div class="text-muted fs-7">Entradas</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-dashed border-danger rounded p-4 text-center">
                                    <div class="fs-2x fw-bold text-danger">{{ $stats['saidas'] }}</div>
                                    <div class="text-muted fs-7">Saídas</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border border-dashed border-info rounded p-4 text-center">
                                    <div class="fs-2x fw-bold text-info">{{ $stats['movimentacoes'] }}</div>
                                    <div class="text-muted fs-7">Movimentações</div>
                                </div>
                            </div>
                        </div>

                        @if($lastReading)
                        <div class="mt-5 p-4 bg-light-primary rounded">
                            <div class="fs-6 fw-bold text-primary mb-2">📍 Última Localização:</div>
                            <div class="fs-5 text-gray-800">{{ $lastReading->location }}</div>
                            <div class="text-muted fs-8 mt-1">{{ $lastReading->read_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Histórico Completo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-clock-history text-primary fs-2"></i>
                    <span class="ms-2">Histórico Completo - {{ $readings->count() }} registros</span>
                </h3>
                <div class="card-toolbar">
                    <button onclick="exportToPDF()" class="btn btn-sm btn-light-primary me-2">
                        <i class="bi bi-file-pdf"></i> Exportar PDF
                    </button>
                    <button onclick="window.print()" class="btn btn-sm btn-light-success">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-hover align-middle gs-0 gy-3" id="historyTable">
                        <thead>
                            <tr class="fs-7 fw-bold text-gray-600 border-bottom border-gray-300">
                                <th class="min-w-50px">#</th>
                                <th class="min-w-150px">Data/Hora</th>
                                <th class="min-w-150px">Leitor</th>
                                <th class="min-w-200px">Localização</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px">Temperatura</th>
                                <th class="min-w-100px">Sinal</th>
                                <th class="min-w-200px">Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($readings as $index => $reading)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td>
                                    <div class="text-gray-800 fw-bold">{{ $reading->read_at->format('d/m/Y') }}</div>
                                    <div class="text-muted fs-8">{{ $reading->read_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary">{{ $reading->reader_id }}</span>
                                </td>
                                <td>
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    {{ $reading->location }}
                                </td>
                                <td>
                                    @if($reading->status == 'entrada')
                                        <span class="badge badge-success">
                                            <i class="bi bi-arrow-down-circle"></i> Entrada
                                        </span>
                                    @elseif($reading->status == 'saida')
                                        <span class="badge badge-danger">
                                            <i class="bi bi-arrow-up-circle"></i> Saída
                                        </span>
                                    @else
                                        <span class="badge badge-info">
                                            <i class="bi bi-arrow-left-right"></i> Movimentação
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($reading->temperature)
                                        <span class="text-primary">
                                            <i class="bi bi-thermometer-half"></i> {{ number_format($reading->temperature, 1) }}°C
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($reading->signal_strength)
                                        <span class="text-success">
                                            <i class="bi bi-reception-4"></i> {{ $reading->signal_strength }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-8">{{ $reading->notes ?? '-' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-10">
                                    <i class="bi bi-inbox fs-3x d-block mb-3"></i>
                                    Nenhum registro encontrado para esta tag no período selecionado
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('custom-js')
<script>
function exportToPDF() {
    alert('Funcionalidade de exportação PDF será implementada em breve!');
    // TODO: Implementar exportação PDF
}
</script>

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