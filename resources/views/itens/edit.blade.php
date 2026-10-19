@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8 col-xl-6">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Editar Registro</h3>
                <a href="/itens/{{ $item->id }}" class="btn btn-secondary btn-sm">
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
                        <input type="text" class="form-control" name="codpes" id="codpes"
                               list="lista-pessoas" autocomplete="off" value="{{ old('codpes', $item->codpes) }}">
                        <datalist id="lista-pessoas"></datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" id="nome" value="{{ old('nome', $item->nome) }}">
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-5 mb-3">
                            <label class="form-label">Tipo do Documento</label>
                            <select class="form-select" name="tipo_documento" id="tipo_documento">
                                @foreach(\App\Models\Item::TIPOS_DOCUMENTO as $tipo)
                                    <option value="{{ $tipo }}" @selected(old('tipo_documento', $item->tipo_documento ?? 'CPF') === $tipo)>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-7 mb-3">
                            <label class="form-label">Documento</label>
                            <input type="text" class="form-control" name="documento" id="documento" value="{{ old('documento', $item->documento) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Patrimônio</label>
                        <input type="text" class="form-control" name="patrimonio" id="patrimonio"
                               list="lista-patrimonios" autocomplete="off" value="{{ old('patrimonio', $item->patrimonio) }}">
                        <datalist id="lista-patrimonios"></datalist>
                        <div id="info-patrimonio" class="form-text"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número de Série</label>
                        <input type="text" class="form-control" name="numero_serie" id="numero_serie" value="{{ old('numero_serie', $item->numero_serie) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observação</label>
                        <textarea class="form-control" name="observacao" id="observacao" rows="3">{{ old('observacao', $item->observacao) }}</textarea>
                    </div>

                    {{-- Upload de novas fotos --}}
                    <div class="mb-3">
                        <label class="form-label">Adicionar fotos</label>
                        <div class="mb-2">
                            <video id="video" autoplay class="img-thumbnail w-100 mb-2 d-none" style="max-height: 200px;"></video>
                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="ligarWebcam()">
                                    <i class="fas fa-camera"></i> Ligar Câmera
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="pararWebcam()">
                                    Desligar
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="tirarFoto()" id="btnTirar" disabled>
                                    Tirar Foto
                                </button>
                            </div>
                        </div>
                        <input type="file" class="form-control mb-2" id="arquivo" accept="image/*" multiple onchange="adicionarArquivos()">
                        <div id="preview-fotos" class="foto-gallery"></div>
                        <div id="fotos-hidden"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar
                        </button>
                    </div>
                </form>

                {{-- Fotos existentes --}}
                @if($item->fotos->isNotEmpty())
                <div class="mt-4">
                    <label class="form-label"><strong>Fotos cadastradas</strong></label>
                    <div class="foto-gallery">
                        @foreach($item->fotos as $foto)
                        <div class="foto-thumb">
                            <img src="{{ $foto->base64 }}" alt="Foto">
                            <form action="/fotos/{{ $foto->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-danger foto-thumb-remove"
                                        onclick="return confirm('Remover foto?')"
                                        title="Remover">✕</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let fotosParaEnviar = @json(old('fotos', []));

function atualizarPreviews() {
    const container = document.getElementById('preview-fotos');
    const hidden = document.getElementById('fotos-hidden');
    container.innerHTML = '';
    hidden.innerHTML = '';

    fotosParaEnviar.forEach((src, i) => {
        const div = document.createElement('div');
        div.className = 'foto-thumb';
        div.innerHTML = `
            <img src="${src}" alt="Foto">
            <button type="button" class="btn btn-danger foto-thumb-remove"
                onclick="removerFoto(${i})">✕</button>`;
        container.appendChild(div);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'fotos[]';
        input.value = src;
        hidden.appendChild(input);
    });
}

function removerFoto(index) {
    fotosParaEnviar.splice(index, 1);
    atualizarPreviews();
}

function adicionarArquivos() {
    const input = document.getElementById('arquivo');
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            fotosParaEnviar.push(e.target.result);
            atualizarPreviews();
        };
        reader.readAsDataURL(file);
    });
}

function ligarWebcam() {
    navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        .then(s => {
            stream = s;
            const video = document.getElementById('video');
            video.srcObject = s;
            video.classList.remove('d-none');
            document.getElementById('btnTirar').disabled = false;
        })
        .catch(() => alert('Não foi possível acessar a webcam'));
}

function pararWebcam() {
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        document.getElementById('video').srcObject = null;
        document.getElementById('video').classList.add('d-none');
        document.getElementById('btnTirar').disabled = true;
    }
}

function tirarFoto() {
    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    fotosParaEnviar.push(canvas.toDataURL('image/jpeg'));
    atualizarPreviews();
    pararWebcam();
}

window.addEventListener('beforeunload', pararWebcam);

// Restaura as fotos já anexadas quando o form volta com erro de validação
atualizarPreviews();
</script>
@endsection

@section('javascripts_bottom')
@parent
@include('itens.partials.autocomplete')
@endsection