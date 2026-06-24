@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Registros de Entrada/Saída</h3>
        @can('admin')
        <a href="/itens/create" class="btn btn-primary">
            + Novo Registro
        </a>
        @endcan
    </div>
    <div class="card-body">
        @if($itens->isEmpty())
            <div class="alert alert-info text-center">
                <h4>Nenhum registro encontrado</h4>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead style="background-color: #002a5e; color: white;">
                    <tr>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>NºUSP</th>
                        <th>Nome</th>
                        <th>Patrimônio</th>
                        <th width="200px">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itens as $item)
                    <tr>
                        <td>{{ $item->origem }}</td>
                        <td>{{ $item->destino }}</td>
                        <td>{{ $item->codpes }}</td>
                        <td>{{ $item->nome }}</td>
                        <td>{{ $item->patrimonio }}</td>
                        <td class="d-flex gap-2">
                            <a href="/itens/{{ $item->id }}" class="btn btn-sm btn-info">Ver</a>
                            @can('admin')
                                <a href="/itens/{{ $item->id }}/edit" class="btn btn-sm btn-warning">Editar</a>
                                <form action="/itens/{{ $item->id }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Apagar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection