<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ItemCrudTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_example(): void
    {
        $this->browse(function (Browser $browser) {
            // Login como admin
            $browser->visit('/login')
                ->type('#callback', 'http://controleportarias/callback')
                ->type('#loginUsuario', '1111')      
                ->press('Login')
                ->pause('100');

            // Vai diretamente para a lista de registros
            $browser->clickLink('Registros')
                ->assertPathIs('/itens');
            
            // Faz novo registro de item
            $browser->clickLink('Novo Registro')
                ->assertPathIs('/itens/create')
                ->assertsee('Novo Registro');

            // Preenche form sem NºUSP (teste erro)
            $browser->type('origem', 'ADM-FFLCH')
                ->type('destino', 'Letras')
                ->press('Registrar')
                ->pause(1000)
                ->assertSee('O nome é obrigatório quando não há NºUSP.')
                ->assertSee('O tipo do documento é obrigatório quando não há NºUSP.')
                ->assertSee('O documento é obrigatório quando não há NºUSP.')
                ->type('nome', 'Zé Ninguém')
                ->type('tipo_documento', 'RG')
                ->type('documento', '852963741')
                ->type('numero_serie', '7895324')
                ->type('observacao', 'Texto sobre o item')
                ->press('Registrar')
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
                ->clickLink('Editar')
                ->assertSee('Editar Registro')
                ->type('nome', 'Zé Ninguém-editado')
                ->press('Salvar')
                ->pause(100)
                ->assertSee('Registro editado com sucesso!');

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
            $browser->assertPathIs('/itens')
                ->assertSee('Item registrado com sucesso!')
                ->assertSee('Letras')
                ->assertSee('ADM-FFLCH')
                ->assertSee('João sem braço')
                ->assertSee('13821998')
                ->assertSee('200.7894.98');

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
