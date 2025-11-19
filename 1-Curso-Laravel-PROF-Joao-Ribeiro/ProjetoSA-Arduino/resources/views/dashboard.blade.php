@extends('layouts.app')

@section('title', 'Dashboard - SmartLOG')

@section('toolbar')
    <div class="toolbar py-5 py-lg-15" id="kt_toolbar" style="background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);">
        <div id="kt_toolbar_container" class="container-xxl d-flex flex-stack flex-wrap">
            <div class="page-title d-flex flex-column me-3">
                <h1 class="d-flex text-white fw-bolder my-1 fs-3">Dashboard RFID</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                    <li class="breadcrumb-item text-white opacity-75">
                        <a href="{{ url('/') }}" class="text-white text-hover-primary">SmartLOG</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-white opacity-75 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-white opacity-75">Dashboard</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light-primary" onclick="refreshDashboard()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <div class="content flex-row-fluid" id="kt_content">
        
        <!-- Stats Cards -->
        <div class="row g-5 g-xl-8 mb-5">
            <div class="col-xl-3">
                <div class="card card-xl-stretch">
                    <div class="card-body">
                        <i class="bi bi-broadcast text-danger fs-2x mb-3"></i>
                        <div class="fw-bold text-gray-800 fs-2x mb-2">{{ number_format($stats['total_readings_today']) }}</div>
                        <div class="fw-semibold text-gray-600 fs-7">Leituras Hoje</div>
                        <div class="mt-3">
                            <span class="badge badge-light-success">
                                <i class="bi bi-arrow-up"></i> Entrada: {{ $stats['entradas_hoje'] }}
                            </span>
                            <span class="badge badge-light-danger ms-2">
                                <i class="bi bi-arrow-down"></i> Saída: {{ $stats['saidas_hoje'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card card-xl-stretch">
                    <div class="card-body">
                        <i class="bi bi-wifi text-primary fs-2x mb-3"></i>
                        <div class="fw-bold text-gray-800 fs-2x mb-2">{{ $stats['readers_online'] }}/{{ $stats['total_readers'] }}</div>
                        <div class="fw-semibold text-gray-600 fs-7">Leitores Online</div>
                        <div class="mt-3">
                            <div class="progress h-6px">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $stats['total_readers'] > 0 ? ($stats['readers_online'] / $stats['total_readers']) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card card-xl-stretch">
                    <div class="card-body">
                        <i class="bi bi-tag text-warning fs-2x mb-3"></i>
                        <div class="fw-bold text-gray-800 fs-2x mb-2">{{ number_format($stats['total_tags']) }}</div>
                        <div class="fw-semibold text-gray-600 fs-7">Tags Ativas</div>
                        <div class="mt-3">
                            <span class="badge badge-light-primary">
                                <i class="bi bi-box"></i> Produtos ativos
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card card-xl-stretch">
                    <div class="card-body">
                        <i class="bi bi-arrow-left-right text-info fs-2x mb-3"></i>
                        <div class="fw-bold text-gray-800 fs-2x mb-2">{{ number_format($stats['movimentacoes_hoje']) }}</div>
                        <div class="fw-semibold text-gray-600 fs-7">Movimentações</div>
                        <div class="mt-3">
                            <span class="text-muted fs-8">Registros internos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 g-xl-8">
            <!-- Leituras Recentes -->
            <div class="col-xl-8">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">📡 Leituras Recentes</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Últimas 10 leituras registradas</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-success pulse pulse-success">
                                <span class="pulse-ring"></span>
                                Ao vivo
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed align-middle gs-0 gy-3" id="readings-table">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-600 border-bottom-0">
                                        <th class="min-w-100px">Tag ID</th>
                                        <th class="min-w-200px">Produto</th>
                                        <th class="min-w-150px">Leitor</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px">Horário</th>
                                    </tr>
                                </thead>
                                <tbody id="readings-tbody">
                                    @forelse($recentReadings as $reading)
                                    <tr>
                                        <td>
                                            <span class="badge badge-light-primary fs-7">
                                                <i class="bi bi-tag-fill"></i> {{ $reading->tag_id }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold fs-6">{{ $reading->product_name ?? 'N/A' }}</span>
                                            @if($reading->product_code)
                                            <br><span class="text-muted fs-8">Cód: {{ $reading->product_code }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="bi bi-geo-alt text-primary"></i>
                                            <span class="text-gray-800 fs-7">{{ $reading->location ?? 'Não especificado' }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusConfig = [
                                                    'entrada' => ['class' => 'success', 'icon' => 'arrow-down-circle', 'text' => 'Entrada'],
                                                    'saida' => ['class' => 'danger', 'icon' => 'arrow-up-circle', 'text' => 'Saída'],
                                                    'movimentacao' => ['class' => 'info', 'icon' => 'arrow-left-right', 'text' => 'Movimentação'],
                                                ];
                                                $config = $statusConfig[$reading->status] ?? $statusConfig['movimentacao'];
                                            @endphp
                                            <span class="badge badge-{{ $config['class'] }} fs-7">
                                                <i class="bi bi-{{ $config['icon'] }}"></i> {{ $config['text'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted fs-7">{{ $reading->read_at->format('d/m H:i:s') }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-10">
                                            <i class="bi bi-inbox fs-3x d-block mb-3"></i>
                                            <div class="fs-5 fw-bold">Nenhuma leitura registrada ainda</div>
                                            <div class="fs-7 text-muted mt-2">Aguardando dados do ESP32...</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status dos Leitores -->
            <div class="col-xl-4">
                <div class="card card-xl-stretch mb-5 mb-xl-8">
                    <div class="card-header border-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">🔌 Status dos Leitores</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Monitoramento em tempo real</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        @forelse($readers as $reader)
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-{{ $reader->isOnline() ? 'success' : 'danger' }}">
                                    <i class="bi bi-broadcast fs-2x text-{{ $reader->isOnline() ? 'success' : 'danger' }}"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="text-gray-800 fw-bold fs-7">{{ $reader->name }}</span>
                                <span class="text-muted fs-8">
                                    <i class="bi bi-geo-alt"></i> {{ $reader->location }}
                                </span>
                            </div>
                            <div class="text-end">
                                @if($reader->isOnline())
                                    <span class="badge badge-success">Online</span>
                                    <div class="text-muted fs-8 mt-1">
                                        {{ $reader->readings_count ?? 0 }} leituras hoje
                                    </div>
                                @else
                                    <span class="badge badge-danger">Offline</span>
                                    <div class="text-muted fs-8 mt-1">
                                        {{ $reader->last_ping ? $reader->last_ping->diffForHumans() : 'Nunca' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)
                        <div class="separator my-3"></div>
                        @endif
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-wifi-off fs-3x d-block mb-3"></i>
                            Nenhum leitor cadastrado
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Top Tags -->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-bold text-gray-800">📊 Leituras por Hora</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="readingsChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card card-xl-stretch mb-xl-8">
                    <div class="card-header border-0">
                        <h3 class="card-title fw-bold text-gray-800">🏆 Top 5 Tags Mais Lidas</h3>
                    </div>
                    <div class="card-body">
                        @forelse($topTags as $index => $tag)
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <span class="text-primary fw-bold">{{ $index + 1 }}</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold d-block">{{ $tag->product_name ?? $tag->tag_id }}</span>
                                <span class="text-muted fs-8">{{ $tag->tag_id }}</span>
                            </div>
                            <span class="badge badge-light-primary">{{ $tag->total }} leituras</span>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bar-chart fs-3x d-block mb-3"></i>
                            Sem dados suficientes
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

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
}

.badge-light-primary {
    background-color: rgba(54, 153, 255, 0.1) !important;
    color: #3699FF !important;
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

/* Garantir que os ícones apareçam */
.bi {
    display: inline-block !important;
}

/* Tabela responsiva */
#readings-table td {
    vertical-align: middle !important;
    padding: 1rem !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Dados para o gráfico
    const readingsData = @json($readingsByHour);
    
    // Criar gráfico
    const ctx = document.getElementById('readingsChart');
    if (ctx && readingsData.length > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: readingsData.map(item => item.hour + 'h'),
                datasets: [{
                    label: 'Leituras',
                    data: readingsData.map(item => item.total),
                    borderColor: '#dc2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Auto-refresh a cada 30 segundos
    let autoRefreshInterval;
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            refreshReadings();
        }, 30000);
    }

    function refreshReadings() {
        fetch('{{ url("/api/rfid/recent") }}')
            .then(response => response.json())
            .then(data => {
                updateReadingsTable(data);
            })
            .catch(error => console.error('Erro ao atualizar:', error));
    }

    function refreshDashboard() {
        location.reload();
    }

    function updateReadingsTable(readings) {
        const tbody = document.getElementById('readings-tbody');
        if (!tbody || readings.length === 0) return;

        let html = '';
        readings.slice(0, 10).forEach(reading => {
            const statusBadge = getStatusBadge(reading.status);
            
            html += `
                <tr class="new-reading">
                    <td><span class="badge badge-light-primary"><i class="bi bi-tag-fill"></i> ${reading.tag_id}</span></td>
                    <td><span class="text-gray-800 fw-bold">${reading.product_name || 'N/A'}</span></td>
                    <td><i class="bi bi-geo-alt text-primary"></i> ${reading.location || 'N/A'}</td>
                    <td>${statusBadge}</td>
                    <td class="text-muted fs-8">${formatDate(reading.read_at)}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function getStatusBadge(status) {
        const badges = {
            'entrada': '<span class="badge badge-success"><i class="bi bi-arrow-down-circle"></i> Entrada</span>',
            'saida': '<span class="badge badge-danger"><i class="bi bi-arrow-up-circle"></i> Saída</span>',
            'movimentacao': '<span class="badge badge-info"><i class="bi bi-arrow-left-right"></i> Movimentação</span>'
        };
        return badges[status] || badges.movimentacao;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // Iniciar auto-refresh
    startAutoRefresh();
</script>
@endsection