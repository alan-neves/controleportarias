@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Editar Registro</h3>
        <a href="/itens/{{ $item->id }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="/itens/{{ $item->id }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label class="form-label">Origem</label>
                <input type="text" class="form-control" name="origem" id="origem" value="{{ old('origem', $item->origem) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Destino</label>
                <input type="text" class="form-control" name="destino" id="destino" value="{{ old('destino', $item->destino) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">NºUSP</label>
                <input type="text" class="form-control" name="codpes" id="codpes" value="{{ old('codpes', $item->codpes) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" id="nome" value="{{ old('nome', $item->nome) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo do Documento</label>
                <input type="text" class="form-control" name="tipo_documento" id="tipo_documento" value="{{ old('tipo_documento', $item->tipo_documento) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Documento</label>
                <input type="text" class="form-control" name="documento" id="documento" value="{{ old('documento', $item->documento) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Patrimônio</label>
                <input type="text" class="form-control" name="patrimonio" id="patrimonio" value="{{ old('patrimonio', $item->patrimonio) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Número de Série</label>
                <input type="text" class="form-control" name="numero_serie" id="numero_serie" value="{{ old('numero_serie', $item->numero_serie) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Observação</label>
                <textarea class="form-control" name="observacao" id="observacao" rows="3">{{ old('observacao', $item->observacao) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Salvar
            </button>
        </form>
    </div>
</div>
@endsection