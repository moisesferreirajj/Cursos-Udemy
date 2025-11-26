@extends('header')

@section('title', isset($reader) ? 'Editar Leitor RFID' : 'Novo Leitor RFID')

@section('content')
<div class="container-xxl py-4">
    <div class="mb-4">
        <a href="{{ route('gerenciar.readers.index') }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-2"></i>Voltar para lista
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-router me-2"></i>
                        {{ isset($reader) ? 'Editar Leitor RFID' : 'Novo Leitor RFID' }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ isset($reader) ? route('gerenciar.readers.update', $reader->id) : route('gerenciar.readers.store') }}" 
                          method="POST">
                        @csrf
                        @if(isset($reader))
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ID do Leitor *</label>
                                <input type="text" 
                                       class="form-control @error('reader_id') is-invalid @enderror" 
                                       name="reader_id" 
                                       value="{{ old('reader_id', $reader->reader_id ?? '') }}" 
                                       placeholder="Ex: READER_ENTRADA"
                                       required>
                                @error('reader_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name', $reader->name ?? '') }}" 
                                       placeholder="Ex: Leitor Entrada Principal"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Localização *</label>
                                <input type="text" 
                                       class="form-control @error('location') is-invalid @enderror" 
                                       name="location" 
                                       value="{{ old('location', $reader->location ?? '') }}" 
                                       placeholder="Ex: Setor de Estoque - Porta 1"
                                       required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Endereço IP</label>
                                <input type="text" 
                                       class="form-control @error('ip_address') is-invalid @enderror" 
                                       name="ip_address" 
                                       value="{{ old('ip_address', $reader->ip_address ?? '') }}" 
                                       placeholder="Ex: 192.168.1.100">
                                @error('ip_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                                <option value="online" {{ old('status', $reader->status ?? '') == 'online' ? 'selected' : '' }}>
                                    Online
                                </option>
                                <option value="offline" {{ old('status', $reader->status ?? '') == 'offline' ? 'selected' : '' }}>
                                    Offline
                                </option>
                                <option value="maintenance" {{ old('status', $reader->status ?? '') == 'maintenance' ? 'selected' : '' }}>
                                    Manutenção
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Informações adicionais sobre o leitor...">{{ old('description', $reader->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('gerenciar.readers.index') }}" class="btn btn-secondary">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ isset($reader) ? 'Atualizar' : 'Cadastrar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection