<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCrudTest extends DuskTestCase
{
    use DatabaseTruncation;

    /**
     * Cria um arquivo de imagem fake e o copia para um caminho
     * com a extensão correta, necessário para que o navegador
     * (via Dusk attach) detecte o MIME type certo ao ler o arquivo.
     */
    private function fakeImagePath(string $nomeArquivo, int $largura = 640, int $altura = 480): string
    {
        $fake = UploadedFile::fake()->image($nomeArquivo, $largura, $altura);
        $destino = sys_get_temp_dir() . '/' . Str::uuid() . '_' . $nomeArquivo;
        copy($fake->getPathname(), $destino);
        return $destino;
    }

    /**
     * Teste completo do CRUD de itens:
     * - registro sem NºUSP (validação de nome/documento)
     * - upload de foto via arquivo
     * - visualização, remoção de foto, edição
     * - exclusão do registro
     * - registro com NºUSP e patrimônio (mutator grava sem pontos)
     */
    public function test_example(): void
    {
        $this->browse(function (Browser $browser) {
            $foto1Path = $this->fakeImagePath('foto1.jpeg');
            $foto2Path = $this->fakeImagePath('foto2.png');

            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://controleportarias/callback')
                ->type('#loginUsuario', '1111')
                ->press('Login')
                ->pause(100);

            // Vai diretamente para a lista de registros
            $browser->clickLink('Registros')
                ->assertPathIs('/itens');

            // Faz novo registro de item
            $browser->clickLink('Novo Registro')
                ->assertPathIs('/itens/create')
                ->assertSee('Novo Registro');

            // Preenche form sem NºUSP (teste de validação)
            $browser->type('origem', 'ADM-FFLCH')
                ->type('destino', 'Letras')
                ->press('Registrar')
                ->pause(1000)
                ->assertSee('O nome é obrigatório quando não há NºUSP.')
                ->assertSee('O documento é obrigatório quando não há NºUSP.')
                ->type('nome', 'Zé Ninguém')
                // AJUSTE 1: tipo_documento agora é <select>, não input de texto
                ->select('tipo_documento', 'RG')
                ->type('documento', '852963741')
                ->type('numero_serie', '7895324')
                ->type('observacao', 'Texto sobre o item');

            // Sobe a foto antes de registrar
            $browser->attach('#arquivo', $foto1Path)
                ->waitFor('.foto-thumb', 5)
                ->assertVisible('.foto-thumb img');

            $browser->press('Registrar')
                ->pause(1000);

            // Verifica se o Item foi registrado
            $browser->assertPathIs('/itens')
                ->assertSee('Item registrado com sucesso!')
                ->assertSee('Letras')
                ->assertSee('ADM-FFLCH')
                ->assertSee('Zé Ninguém');

            // Teste view show do item
            $browser->clickLink('Ver')
                ->assertSee('Registro')
                ->assertPresent('.foto-thumb-img');

            // Exclui a foto pelo X
            $browser->press('✕')
                ->acceptDialog()
                ->pause(500)
                ->assertMissing('.foto-thumb-img');

            // Edita: muda o nome e sobe uma nova foto
            $browser->clickLink('Editar')
                ->assertSee('Editar Registro')
                ->type('nome', 'Zé Ninguém-editado')
                ->attach('#arquivo', $foto2Path)
                ->waitFor('.foto-thumb', 5)
                ->assertVisible('.foto-thumb img')
                ->press('Salvar')
                ->pause(100)
                ->assertSee('Registro editado com sucesso!');

            // Confere a foto na tela de detalhes
            $browser->assertPresent('.foto-thumb-img');

            // Apaga o registro
            $browser->press('Apagar')
                ->acceptDialog()
                ->pause(100)
                ->assertPathIs('/itens')
                ->assertSee('Registro apagado com sucesso');

            // Faz novo registro de item
            $browser->clickLink('Novo Registro')
                ->assertPathIs('/itens/create');

            // Preenche form com NºUSP
            $browser->type('origem', 'ADM-FFLCH')
                ->type('destino', 'Letras')
                ->type('codpes', '13821998')
                ->type('nome', 'João sem braço')
                ->type('patrimonio', '200.7894.98')
                ->type('observacao', 'Texto sobre o item')
                ->press('Registrar')
                ->pause(100);

            // Verifica se o Item foi registrado
            // AJUSTE 2: o mutator grava o patrimônio sem pontos ("200789498"),
            // então o valor exibido na lista não é mais "200.7894.98"
            $browser->assertPathIs('/itens')
                ->assertSee('Item registrado com sucesso!')
                ->assertSee('Letras')
                ->assertSee('ADM-FFLCH')
                ->assertSee('João sem braço')
                ->assertSee('13821998')
                ->assertSee('200789498');

            // Teste view show do item
            $browser->clickLink('Ver')
                ->assertSee('Registro')
                ->clickLink('Editar')
                ->assertSee('Editar Registro')
                ->type('nome', 'João sem braço-editado')
                ->press('Salvar')
                ->pause(100)
                ->assertSee('Registro editado com sucesso!');

            // Apaga o registro
            $browser->press('Apagar')
                ->acceptDialog()
                ->pause(100)
                ->assertPathIs('/itens')
                ->assertSee('Registro apagado com sucesso');
        });
    }
}