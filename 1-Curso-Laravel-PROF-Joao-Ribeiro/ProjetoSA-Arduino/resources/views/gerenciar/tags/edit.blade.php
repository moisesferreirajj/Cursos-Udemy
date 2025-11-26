@extends('header')

@section('title', 'Editar Tag RFID')

@section('content')
<div class="container-xxl py-4">
    <div class="mb-4">
        <a href="{{ route('gerenciar.tags.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i>Voltar para lista
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil me-2"></i>
                            Editar Tag RFID
                        </h4>
                        <code class="bg-white px-2 py-1 rounded">{{ $tag->tag_id }}</code>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('gerenciar.tags.update', $tag->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informações Básicas -->
                        <div class="border-bottom pb-3 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-info-circle me-2"></i>Informações Básicas
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ID da Tag *</label>
                                    <input type="text" 
                                           class="form-control @error('tag_id') is-invalid @enderror" 
                                           name="tag_id" 
                                           value="{{ old('tag_id', $tag->tag_id) }}" 
                                           required>
                                    <small class="text-muted">Altere apenas se necessário</small>
                                    @error('tag_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Código do Produto</label>
                                    <input type="text" 
                                           class="form-control @error('product_code') is-invalid @enderror" 
                                           name="product_code" 
                                           value="{{ old('product_code', $tag->product_code) }}" 
                                           placeholder="Ex: PROD-2024-001">
                                    @error('product_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nome do Produto *</label>
                                <input type="text" 
                                       class="form-control @error('product_name') is-invalid @enderror" 
                                       name="product_name" 
                                       value="{{ old('product_name', $tag->product_name) }}" 
                                       required>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          rows="3">{{ old('description', $tag->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Classificação -->
                        <div class="border-bottom pb-3 mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-grid me-2"></i>Classificação
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                        <option value="pallet" {{ old('type', $tag->type) == 'pallet' ? 'selected' : '' }}>
                                            🏗️ Pallet
                                        </option>
                                        <option value="produto" {{ old('type', $tag->type) == 'produto' ? 'selected' : '' }}>
                                            📦 Produto
                                        </option>
                                        <option value="ferramenta" {{ old('type', $tag->type) == 'ferramenta' ? 'selected' : '' }}>
                                            🔧 Ferramenta
                                        </option>
                                        <option value="outro" {{ old('type', $tag->type) == 'outro' ? 'selected' : '' }}>
                                            ❓ Outro
                                        </option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" 
                                           class="form-control @error('category') is-invalid @enderror" 
                                           name="category" 
                                           value="{{ old('category', $tag->category) }}" 
                                           list="category-suggestions">
                                    <datalist id="category-suggestions">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">
                                        @endforeach
                                    </datalist>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-toggle-on me-2"></i>Status
                            </h5>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Status Atual *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                        <option value="ativo" {{ old('status', $tag->status) == 'ativo' ? 'selected' : '' }}>
                                            ✅ Ativo - Tag em uso normal
                                        </option>
                                        <option value="inativo" {{ old('status', $tag->status) == 'inativo' ? 'selected' : '' }}>
                                            ⏸️ Inativo - Tag temporariamente fora de uso
                                        </option>
                                        <option value="perdido" {{ old('status', $tag->status) == 'perdido' ? 'selected' : '' }}>
                                            ⚠️ Perdido - Tag não localizada
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Estatísticas -->
                        <div class="mb-4 bg-light p-3 rounded">
                            <h6 class="text-muted mb-3">
                                <i class="bi bi-graph-up me-2"></i>Estatísticas da Tag
                            </h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Total de Leituras</small>
                                    <strong class="fs-5 text-primary">{{ $tag->readings_count ?? 0 }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Última Localização</small>
                                    <strong>{{ $tag->getCurrentLocation() }}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Última Leitura</small>
                                    <strong>
                                        @if($tag->lastReading)
                                            {{ $tag->lastReading->read_at->diffForHumans() }}
                                        @else
                                            Sem leituras
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('gerenciar.tags.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="bi bi-check-circle me-2"></i>Atualizar Tag
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Histórico de Leituras -->
            @if($tag->readings && $tag->readings->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Últimas Leituras
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Leitor</th>
                                    <th>Localização</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tag->readings as $reading)
                                <tr>
                                    <td class="small">{{ $reading->read_at->format('d/m/Y H:i:s') }}</td>
                                    <td><code class="small">{{ $reading->reader_id }}</code></td>
                                    <td class="small">{{ $reading->location }}</td>
                                    <td>
                                        <span class="badge badge-sm bg-{{ $reading->status == 'entrada' ? 'success' : ($reading->status == 'saida' ? 'danger' : 'info') }}">
                                            {{ ucfirst($reading->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection