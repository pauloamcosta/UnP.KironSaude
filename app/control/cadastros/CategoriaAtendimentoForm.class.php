<?php

class CategoriaAtendimentoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        // Título da página de formulário de cadastro
        parent::setTitle( "Cadastro de Categoria de Atendimento" );

        // Definição do tamanho do formulário na tela.
        parent::setSize( 0.500, 0.450 );

        // Caractere para informação de campo obrigatório
        $redstar = '<font color="red"><b>*</b></font>';

        // Definição dos campos para digitação - id THidden, não é apresentado
        $id        = new THidden( "id" ); 
        $nome      = new TEntry( "nome" );
        $nome->setProperty("title", "O campo e obrigatorio");
        $nome->setSize("100%");
        $nome->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        // Definição do formulário, com linha de "campos obrigatórios" como título
        $this->form = new BootstrapFormBuilder( "form_categoriaatendimento" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        // Inclusão dos campos definidos anteriormente ao formulário
        $this->form->addFields( [ $id ] );
        $this->form->addFields([new TLabel("Nome: $redstar")], [$nome]);
        
        // Botões de ação
        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        // Gera o conteúdo a ser apresentado na tela
        // Conteúdo: blocos com o formulário de cadastro/alteração
        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        parent::add( $container );
    }

    // Função para inserir os dados do formulário no banco
    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData("CategoriaAtendimentoRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "CategoriaAtendimentoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    // Função para alterar os dados no banco
    public function onEdit( $param )
    {
        try {
            if( isset( $param[ "key" ] ) ) {
                // Se for edição, altera o título do formulário para edição
                parent::setTitle( "Edição de Categoria de Atendimento" );

                TTransaction::open( "database" );

                $object = new CategoriaAtendimentoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}