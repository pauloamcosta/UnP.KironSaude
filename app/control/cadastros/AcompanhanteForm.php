<?php

class AcompanhanteForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Acompanhante" );

        parent::setSize( 0.500, 0.650 );

        $redstar = '<font color="red"><b>*</b></font>';

        $id        = new THidden( "id" ); 

        $cpf       = new TEntry( "cpf" );
        $cpf->placeholder = "CPF";
        $cpf->setSize("25%");
        $cpf->setMask("999.999.999-99");
        $cpf->addValidation( TextFormat::set( "CPF" ), new TRequiredValidator );
        $cpf->addValidation( TextFormat::set( "CPF" ), new TCPFValidator );

        $nome      = new TEntry( "nome" );
        $nome->forceUpperCase();
        $nome->placeholder = "Nome do acompanhante";
        $nome->setSize("100%");
        $nome->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $endereco  = new TEntry( "endereco" );
        $endereco->forceUpperCase();
        $endereco->placeholder = "Endereço completo do acompanhante";
        $endereco->setSize("100%");
        $endereco->addValidation( TextFormat::set( "Endereço" ), new TRequiredValidator );

        $contato      = new TEntry( "contato" );
        $contato->placeholder = "DDD + Número";
        $contato->setSize("25%");
        $contato->setMask("(99)999999999");
        $contato->addValidation( TextFormat::set( "Contato" ), new TRequiredValidator );
        $contato->addValidation( TextFormat::set( "Contato" ), new TMinLengthValidator, array(12));

        $this->form = new BootstrapFormBuilder( "form_acompanhante" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $this->form->addFields( [ $id ] );
        $this->form->addFields([new TLabel("CPF: $redstar")], [$cpf]);
        $this->form->addFields([new TLabel("Nome: $redstar")], [$nome]);
        $this->form->addFields([new TLabel("Endereço: $redstar")], [$endereco]);
        $this->form->addFields([new TLabel("Contato: $redstar")], [$contato]);
        
        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData("AcompanhanteRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "AcompanhanteList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {
            if( isset( $param[ "key" ] ) ) {
                parent::setTitle( "Edição de Acompanhante" );

                TTransaction::open( "database" );

                $object = new AcompanhanteRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}