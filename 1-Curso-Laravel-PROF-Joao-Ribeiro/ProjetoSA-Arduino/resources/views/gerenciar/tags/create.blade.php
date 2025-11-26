@extends('header')

@section('title', 'Nova Tag RFID')

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
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-tags me-2"></i>
                        Nova Tag RFID
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('gerenciar.tags.store') }}" method="POST">
                        @csrf

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
                                           value="{{ old('tag_id') }}" 
                                           placeholder="Ex: a1b2c3d4"
                                           required>
                                    <small class="text-muted">ID único da tag RFID (geralmente em hexadecimal)</small>
                                    @error('tag_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Código do Produto</label>
                                    <input type="text" 
                                           class="form-control @error('product_code') is-invalid @enderror" 
                                           name="product_code" 
                                           value="{{ old('product_code') }}" 
                                           placeholder="Ex: PROD-2024-001">
                                    <small class="text-muted">Código interno do produto/item</small>
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
                                       value="{{ old('product_name') }}" 
                                       placeholder="Ex: Notebook Dell Inspiron 15"
                                       required>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Informações adicionais sobre o item...">{{ old('description') }}</textarea>
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
                                        <option value="">Selecione o tipo</option>
                                        <option value="pallet" {{ old('type') == 'pallet' ? 'selected' : '' }}>
                                            🏗️ Pallet
                                        </option>
                                        <option value="produto" {{ old('type') == 'produto' ? 'selected' : '' }}>
                                            📦 Produto
                                        </option>
                                        <option value="ferramenta" {{ old('type') == 'ferramenta' ? 'selected' : '' }}>
                                            🔧 Ferramenta
                                        </option>
                                        <option value="outro" {{ old('type') == 'outro' ? 'selected' : '' }}>
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
                                           value="{{ old('category') }}" 
                                           placeholder="Ex: Eletrônicos, Ferramentas, Alimentos"
                                           list="category-suggestions">
                                    <datalist id="category-suggestions">
                                        @if(isset($categories))
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat }}">
                                            @endforeach
                                        @else
                                            <option value="Eletrônicos">
                                            <option value="Ferramentas">
                                            <option value="Alimentos">
                                            <option value="Vestuário">
                                            <option value="Matéria-Prima">
                                            <option value="Equipamentos">
                                        @endif
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
                                        <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>
                                            ✅ Ativo - Tag em uso normal
                                        </option>
                                        <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>
                                            ⏸️ Inativo - Tag temporariamente fora de uso
                                        </option>
                                        <option value="perdido" {{ old('status') == 'perdido' ? 'selected' : '' }}>
                                            ⚠️ Perdido - Tag não localizada
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                            <a href="{{ route('gerenciar.tags.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Cadastrar Tag
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dica -->
            <div class="alert alert-info mt-3">
                <i class="bi bi-lightbulb me-2"></i>
                <strong>Dica:</strong> O ID da tag é obtido automaticamente quando o ESP32 faz a primeira leitura. 
                Você pode cadastrar tags antecipadamente ou deixar o sistema criar automaticamente na primeira leitura.
            </div>
        </div>
    </div>
</div>
@endsection