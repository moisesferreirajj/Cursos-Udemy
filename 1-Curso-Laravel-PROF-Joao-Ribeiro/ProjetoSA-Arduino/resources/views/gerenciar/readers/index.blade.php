@extends('header')

@section('title', 'Gerenciar Leitores RFID')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-router me-2"></i>Leitores RFID</h2>
            <p class="text-muted mb-0">Gerenciar dispositivos leitores do sistema</p>
        </div>
        <a href="{{ route('gerenciar.readers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Novo Leitor
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('gerenciar.readers.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small">Buscar</label>
                    <input type="text" class="form-control" name="search" placeholder="ID, nome, localização ou IP..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Manutenção</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Localização</label>
                    <input type="text" class="form-control" name="location" placeholder="Filtrar local..." value="{{ request('location') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Ordenar por</label>
                    <select class="form-select" name="sort_by">
                        <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nome (A-Z)</option>
                        <option value="reader_id" {{ request('sort_by') == 'reader_id' ? 'selected' : '' }}>Reader ID</option>
                        <option value="location" {{ request('sort_by') == 'location' ? 'selected' : '' }}>Localização</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data Cadastro</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID do Leitor</th>
                            <th>
                                Nome
                                @if(request('sort_by', 'name') == 'name')
                                    <i class="bi bi-sort-alpha-down text-primary"></i>
                                @endif
                            </th>
                            <th>Localização</th>
                            <th>IP</th>
                            <th>Status</th>
                            <th>Leituras</th>
                            <th>Último Ping</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($readers as $reader)
                        <tr>
                            <td><code>{{ $reader->reader_id }}</code></td>
                            <td><strong>{{ $reader->name }}</strong></td>
                            <td>
                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                {{ $reader->location }}
                            </td>
                            <td>
                                @if($reader->ip_address)
                                    <code>{{ $reader->ip_address }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($reader->status == 'online')
                                    <span class="badge bg-success">
                                        <i class="bi bi-wifi"></i> Online
                                    </span>
                                @elseif($reader->status == 'maintenance')
                                    <span class="badge bg-warning">
                                        <i class="bi bi-tools"></i> Manutenção
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-wifi-off"></i> Offline
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $reader->readings_count }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $reader->last_ping ? $reader->last_ping->diffForHumans() : 'Nunca' }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('gerenciar.readers.edit', $reader->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('gerenciar.readers.destroy', $reader->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Deseja realmente excluir este leitor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-router" style="font-size: 48px;"></i>
                                <p class="mt-3">Nenhum leitor RFID cadastrado</p>
                                <a href="{{ route('gerenciar.readers.create') }}" class="btn btn-primary btn-sm">
                                    Cadastrar Primeiro Leitor
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($readers->hasPages())
        <div class="card-footer">
            {{ $readers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection