@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Registro</h3>
        <a href="/itens" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        <p><strong>Origem:</strong> {{ $item->origem }}</p>
        <p><strong>Destino:</strong> {{ $item->destino }}</p>
        <p><strong>NºUSP:</strong> {{ $item->codpes }}</p>
        <p><strong>Nome:</strong> {{ $item->nome }}</p>
        <p><strong>Tipo do Documento:</strong> {{ $item->tipo_documento }}</p>
        <p><strong>Documento:</strong> {{ $item->documento }}</p>
        <p><strong>Patrimônio:</strong> {{ $item->patrimonio }}</p>
        <p><strong>Número de Série:</strong> {{ $item->numero_serie }}</p>
        <p><strong>Observação:</strong> {{ $item->observacao }}</p>

        <div class="mt-3 d-flex gap-2">
            @can('admin')
            <a href="/itens/{{ $item->id }}/edit" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form action="/itens/{{ $item->id }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza?')">
                    <i class="fas fa-trash"></i> Apagar
                </button>
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection