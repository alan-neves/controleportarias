<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller
{
    public function index()
    {
        $itens = Item::all();
        return view('itens.index', ['itens' => $itens]);
    }

    public function create()
    {
        return view('itens.create');
    }

    public function store(ItemRequest $request)
    {
        $item = new Item;
        $item->origem = $request->origem;
        $item->destino = $request->destino;
        $item->codpes = $request->codpes;
        $item->nome = $request->nome;
        $item->tipo_documento = $request->tipo_documento;
        $item->documento = $request->documento;
        $item->patrimonio = $request->patrimonio;
        $item->numero_serie = $request->numero_serie;
        $item->observacao = $request->observacao;
        $item->save();

        return redirect('/itens')->with('alert-success', 'Item registrado com sucesso!');
    }

    public function show(Item $item)
    {
        return view('itens.show', ['item' => $item]);
    }

    public function edit(Item $item)
    {
        return view('itens.edit', ['item' => $item]);
    }

    public function update(ItemRequest $request, Item $item)
    {
        $item->origem = $request->origem;
        $item->destino = $request->destino;
        $item->codpes = $request->codpes;
        $item->nome = $request->nome;
        $item->tipo_documento = $request->tipo_documento;
        $item->documento = $request->documento;
        $item->patrimonio = $request->patrimonio;
        $item->numero_serie = $request->numero_serie;
        $item->observacao = $request->observacao;
        $item->save();

        return redirect("/itens/{$item->id}")->with('alert-success', 'Registro editado com sucesso!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect('/itens')->with('alert-success', 'Registro apagado com sucesso!');
    }
}
