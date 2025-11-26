@extends('header')

@section('title', 'Gerenciar Tags RFID')

@section('content')
<div class="container-xxl py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-tags me-2"></i>Tags RFID</h2>
            <p class="text-muted mb-0">Gerenciar tags cadastradas no sistema</p>
        </div>
        <a href="{{ route('gerenciar.tags.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Nova Tag
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('gerenciar.tags.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Buscar Tag/Produto</label>
                    <input type="text" class="form-control" name="search" placeholder="ID ou nome..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tipo</label>
                    <select class="form-select" name="type">
                        <option value="">Todos</option>
                        <option value="pallet" {{ request('type') == 'pallet' ? 'selected' : '' }}>Pallet</option>
                        <option value="produto" {{ request('type') == 'produto' ? 'selected' : '' }}>Produto</option>
                        <option value="ferramenta" {{ request('type') == 'ferramenta' ? 'selected' : '' }}>Ferramenta</option>
                        <option value="outro" {{ request('type') == 'outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        <option value="perdido" {{ request('status') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Categoria</label>
                    <input type="text" class="form-control" name="category" placeholder="Ex: Eletrônicos" value="{{ request('category') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Ordenar por</label>
                    <select class="form-select" name="sort_by">
                        <option value="product_name" {{ request('sort_by', 'product_name') == 'product_name' ? 'selected' : '' }}>Nome (A-Z)</option>
                        <option value="tag_id" {{ request('sort_by') == 'tag_id' ? 'selected' : '' }}>Tag ID</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Data Cadastro</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
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
                            <th style="width: 120px;">Tag ID</th>
                            <th>
                                Produto 
                                @if(request('sort_by', 'product_name') == 'product_name')
                                    <i class="bi bi-sort-alpha-down text-primary"></i>
                                @endif
                            </th>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Localização Atual</th>
                            <th>Leituras</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $tag)
                        <tr>
                            <td><code class="fw-bold">{{ $tag->tag_id }}</code></td>
                            <td>
                                <strong>{{ $tag->product_name }}</strong>
                                @if($tag->description)
                                    <br><small class="text-muted">{{ Str::limit($tag->description, 40) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($tag->product_code)
                                    <code>{{ $tag->product_code }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tag->category)
                                    <span class="badge bg-secondary">{{ $tag->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @switch($tag->type)
                                    @case('pallet')
                                        <span class="badge bg-info">
                                            <i class="bi bi-stack"></i> Pallet
                                        </span>
                                        @break
                                    @case('produto')
                                        <span class="badge bg-primary">
                                            <i class="bi bi-box-seam"></i> Produto
                                        </span>
                                        @break
                                    @case('ferramenta')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-tools"></i> Ferramenta
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-question-circle"></i> Outro
                                        </span>
                                @endswitch
                            </td>
                            <td>
                                @switch($tag->status)
                                    @case('ativo')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Ativo
                                        </span>
                                        @break
                                    @case('inativo')
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-dash-circle"></i> Inativo
                                        </span>
                                        @break
                                    @case('perdido')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-exclamation-triangle"></i> Perdido
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($tag->lastReading)
                                    <small>
                                        <i class="bi bi-geo-alt text-primary"></i>
                                        {{ $tag->lastReading->location }}
                                        <br>
                                        <span class="text-muted">{{ $tag->lastReading->read_at->diffForHumans() }}</span>
                                    </small>
                                @else
                                    <span class="text-muted">Sem leituras</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $tag->readings_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('gerenciar.tags.edit', $tag->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('gerenciar.tags.destroy', $tag->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Deseja realmente excluir esta tag?\n\nAtenção: Isso não excluirá as leituras associadas.')">
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
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-tags" style="font-size: 48px;"></i>
                                <p class="mt-3">Nenhuma tag RFID cadastrada</p>
                                <a href="{{ route('gerenciar.tags.create') }}" class="btn btn-primary btn-sm">
                                    Cadastrar Primeira Tag
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($tags->hasPages())
        <div class="card-footer">
            {{ $tags->links() }}
        </div>
        @endif
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success" style="font-size: 32px;"></i>
                    <h4 class="mt-2 mb-0">{{ $stats['ativo'] }}</h4>
                    <small class="text-muted">Tags Ativas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-secondary">
                <div class="card-body text-center">
                    <i class="bi bi-dash-circle text-secondary" style="font-size: 32px;"></i>
                    <h4 class="mt-2 mb-0">{{ $stats['inativo'] }}</h4>
                    <small class="text-muted">Tags Inativas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 32px;"></i>
                    <h4 class="mt-2 mb-0">{{ $stats['perdido'] }}</h4>
                    <small class="text-muted">Tags Perdidas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up text-primary" style="font-size: 32px;"></i>
                    <h4 class="mt-2 mb-0">{{ number_format($stats['total_leituras']) }}</h4>
                    <small class="text-muted">Total de Leituras</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection