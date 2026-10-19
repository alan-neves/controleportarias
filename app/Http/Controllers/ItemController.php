<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Uspdev\Replicado\Pessoa;
use Uspdev\Replicado\DB;

class ItemController extends Controller
{
    public function index()
    {
        Gate::authorize('admin');

        $itens = Item::all();
        return view('itens.index', ['itens' => $itens]);
    }

    public function create()
    {
        Gate::authorize('admin');

        return view('itens.create');
    }

    public function store(ItemRequest $request, FotoController $fotoController)
    {
        $item = new Item;
        $item->fill($request->validated());
        $item->save();

        $fotoController->salvarFotos($request->input('fotos', []), $item);

        return redirect('/itens')->with('alert-success', 'Item registrado com sucesso!');
    }

    public function show(Item $item)
    {
        Gate::authorize('admin');

        $item->load('fotos');
        return view('itens.show', ['item' => $item]);
    }

    public function edit(Item $item)
    {
        Gate::authorize('admin');

        $item->load('fotos');
        return view('itens.edit', ['item' => $item]);
    }

    public function update(ItemRequest $request, Item $item, FotoController $fotoController)
    {
        $item->fill($request->validated());
        $item->save();

        $fotoController->salvarFotos($request->input('fotos', []), $item);

        return redirect("/itens/{$item->id}")->with('alert-success', 'Registro editado com sucesso!');
    }

    public function destroy(Item $item)
    {
        Gate::authorize('admin');

        foreach ($item->fotos as $foto) {
            $foto->delete();
        }
        $item->delete();

        return redirect('/itens')->with('alert-success', 'Registro apagado com sucesso!');
    }

    // Autocomplete de NºUSP 
    public function buscarPessoas(Request $request)
    {
        Gate::authorize('admin');

        $busca = trim($request->string('q')->toString());
        if (mb_strlen($busca) < 3) {
            return response()->json([]);
        }

        $resultado = Pessoa::procurarPorCodigoOuNome($busca, false);
        if (!is_array($resultado)) { // erro de consulta
            return response()->json([]);
        }

        return response()->json(
            collect($resultado)
                ->take(10)
                ->map(fn ($p) => [
                    'codpes' => (int) $p['codpes'],
                    'nome'   => trim($p['nompesttd']),
                ])
                ->values()
        );
    }

    // Autocomplete de patrimônio
    public function buscarPatrimonios(Request $request)
    {
        Gate::authorize('admin');

        $numpat = preg_replace('/\D/', '', $request->string('q')->toString());

        if (strlen($numpat) < 3) {
            return response()->json([]);
        }

        // numpat é decimal no sybase: converte pra varchar antes do LIKE
        $bens = DB::fetch(
            "SELECT TOP 10 numpat, stabem, epfmarpat, epforibem, numidfpat
            FROM BEMPATRIMONIADO
            WHERE CONVERT(VARCHAR(12), numpat) LIKE '%{$numpat}%'"
        ) ?: []; // DB::fetch devolve FALSE quando vazio

        /*registro vem como array associativo,
        vários vêm como array de arrays — uniformiza:*/
        if (isset($bens['numpat'])) {
            $bens = [$bens];
        }

        return response()->json(
            collect($bens)->map(fn ($b) => $this->formatarBem($b))->values()
        );
    }

    private function formatarBem(array $bem): array
    {
        // array_filter sem callback remove os nulls/vazios de uma vez
        $detalhes = array_filter([
            'Situação' => $bem['stabem'] ?? null,
            'Marca'    => $bem['epfmarpat'] ?? null,
            'Origem'   => $bem['epforibem'] ?? null,
        ]);

        // numidfpat = número de série da etiqueta do bem (quando existir)
        $serie = trim((string) ($bem['numidfpat'] ?? ''));

        return [
            'numpat'       => preg_replace('/\D/', '', (string) $bem['numpat']), // tira o ".0000" do decimal
            'detalhes'     => $detalhes + ($serie !== '' ? ['Série' => $serie] : []),
            'numero_serie' => $serie !== '' ? $serie : null, // null quando o bem não tem série
        ];
    }
}