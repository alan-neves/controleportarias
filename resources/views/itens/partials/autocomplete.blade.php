{{-- Autocomplete replicado: NºUSP (pessoas) e Patrimônio (bens) --}}
{{-- Usado em itens/create e itens/edit via @include dentro de @section('javascripts_bottom') --}}
<script>
(function () {
    const MIN_CHARS = 3;
    const DEBOUNCE_MS = 300;

    // deixa só os dígitos — aceita "200.106863", "200106863", " 200.106.863 "
    const soDigitos = s => s.replace(/\D/g, '');

    const debounce = (fn, ms = DEBOUNCE_MS) => {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

    async function buscar(url, q, abortar) {
        const r = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json' },
            signal: abortar.signal,
        });
        return r.ok ? await r.json() : [];
    }

    function preencherLista(datalist, itens, valor, rotulo) {
        datalist.replaceChildren(...itens.map(i => {
            const o = document.createElement('option');
            o.value = valor(i);
            o.label = rotulo(i);
            return o;
        }));
    }

    // indicador visual fixo sob o campo (o datalist em si não aceita conteúdo customizado)
    function criarIndicador(campo, id) {
        const el = document.createElement('div');
        el.id = id;
        el.className = 'form-text';
        el.style.display = 'none';
        campo.closest('.mb-3').appendChild(el);

        const msg = (icone, texto, cor) =>
            `<i class="fas ${icone} ${cor} me-1"></i>${texto}`;

        return {
            carregando(texto = 'buscando…') {
                el.innerHTML = `<span class="spinner-border spinner-border-sm text-secondary me-1" role="status"></span>${texto}`;
                el.style.display = '';
            },
            sucesso(texto) { el.innerHTML = msg('fa-check-circle', texto, 'text-success'); el.style.display = ''; },
            aviso(texto)   { el.innerHTML = msg('fa-exclamation-circle', texto, 'text-warning'); el.style.display = ''; },
            erro(texto = 'não foi possível consultar agora, tente de novo') {
                el.innerHTML = msg('fa-times-circle', texto, 'text-danger'); el.style.display = '';
            },
            limpar() { el.innerHTML = ''; el.style.display = 'none'; },
        };
    }

    // ---------- NºUSP ----------
    let pessoas = [];
    let abortPessoas = null;
    const campoCodpes = document.getElementById('codpes');
    const campoNome = document.getElementById('nome');
    const statusPessoa = criarIndicador(campoCodpes, 'status-codpes');

    const consultarPessoas = debounce(async () => {
        const q = campoCodpes.value.trim();
        if (q.length < MIN_CHARS) {
            pessoas = [];
            document.getElementById('lista-pessoas').replaceChildren();
            statusPessoa.limpar();
            return;
        }
        abortPessoas?.abort();
        abortPessoas = new AbortController();
        statusPessoa.carregando('buscando na base USP…');
        try {
            pessoas = await buscar('/replicado/pessoas', q, abortPessoas);
        } catch (e) {
            if (e.name === 'AbortError') return; // digitação mais nova cancelou esta busca
            pessoas = [];
            statusPessoa.erro();
            return;
        }
        preencherLista(document.getElementById('lista-pessoas'), pessoas,
            p => p.codpes, p => `${p.codpes} - ${p.nome}`);
        const exata = pessoas.find(p => String(p.codpes) === q);
        if (exata) {
            campoNome.value = exata.nome;
            statusPessoa.sucesso(exata.nome);
        } else if (pessoas.length) {
            statusPessoa.aviso(`${pessoas.length} resultado(s) — escolha um na lista`);
        } else {
            statusPessoa.aviso('nenhuma pessoa encontrada');
        }
    });

    campoCodpes.addEventListener('input', () => {
        const exata = pessoas.find(p => String(p.codpes) === campoCodpes.value.trim());
        if (exata) {
            campoNome.value = exata.nome;
            statusPessoa.sucesso(exata.nome);
        }
        consultarPessoas();
    });

    // ---------- Patrimônio ----------
    let bens = [];
    let abortBens = null;
    const campoPat = document.getElementById('patrimonio');
    const info = document.getElementById('info-patrimonio');
    const campoSerie = document.getElementById('numero_serie');
    const statusBem = criarIndicador(campoPat, 'status-patrimonio');

    function mostrarBem(bem) {
        info.textContent = bem
            ? Object.entries(bem.detalhes).map(([k, v]) => `${k}: ${v}`).join(' | ')
            : '';

        if (bem && bem.numero_serie && !campoSerie.value) {
            campoSerie.value = bem.numero_serie;
        }
        return bem;
    }

    const resumoBem = bem =>
        Object.values(bem.detalhes).join(' | ') || `patrimônio ${bem.numpat} encontrado`;

    const consultarBens = debounce(async () => {
        const digitos = soDigitos(campoPat.value);
        if (digitos.length < MIN_CHARS) {
            bens = [];
            document.getElementById('lista-patrimonios').replaceChildren();
            mostrarBem(null);
            statusBem.limpar();
            return;
        }
        abortBens?.abort();
        abortBens = new AbortController();
        statusBem.carregando('consultando patrimônio…');
        try {
            bens = await buscar('/replicado/patrimonios', digitos, abortBens);
        } catch (e) {
            if (e.name === 'AbortError') return;
            bens = [];
            statusBem.erro();
            return;
        }
        preencherLista(document.getElementById('lista-patrimonios'), bens,
            b => b.numpat, b => `${b.numpat} ${Object.values(b.detalhes).join(' - ')}`);
        const exato = bens.find(b => soDigitos(b.numpat) === digitos);
        if (exato) {
            mostrarBem(exato);
            statusBem.sucesso(resumoBem(exato));
        } else if (bens.length) {
            mostrarBem(null);
            statusBem.aviso(`${bens.length} resultado(s) — escolha um na lista`);
        } else {
            mostrarBem(null);
            statusBem.aviso('patrimônio não encontrado');
        }
    });

    campoPat.addEventListener('input', () => {
        const digitos = soDigitos(campoPat.value);
        const exato = bens.find(b => soDigitos(b.numpat) === digitos);
        if (exato) {
            mostrarBem(exato);
            statusBem.sucesso(resumoBem(exato));
        } else if (digitos.length >= MIN_CHARS) {
            mostrarBem(null);
        }
        consultarBens();
    });
})();
</script>