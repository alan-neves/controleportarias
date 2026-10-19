<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FotoController extends Controller
{
    public function salvarFotos(array $fotoData, Item $item)
    {
        foreach ($fotoData as $fotoBase64) {
            if (empty($fotoBase64)) continue;

            if (strpos($fotoBase64, 'base64,') !== false) {
                $fotoBase64 = base64_decode(explode('base64,', $fotoBase64)[1]);
            }

            // confirma que os bytes decodificados são realmente uma imagem válida
            if (@getimagesizefromstring($fotoBase64) === false) {
                continue;
            }

            $nomeArquivo = Str::uuid() . '.jpg';
            Storage::disk('fotos')->put($nomeArquivo, $fotoBase64);

            Foto::create([
                'item_id' => $item->id,
                'foto' => $nomeArquivo,
            ]);
        }
    }

    // Deleta foto individualmente 
    public function destroy(Foto $foto)
    {
        Gate::authorize('admin');
        
        $foto->delete();
        return back()->with('alert-success', 'Foto removida com sucesso!');
    }
}