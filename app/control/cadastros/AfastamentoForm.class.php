<?php

class AfastamentoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Afastamentos" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_afastamento" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id        = new THidden( "id" );
        $servidor_id = new TCombo('servidor_id');
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');
        $motivo = new TEntry('quantidade_dias');


        $servidor_id->setProperty("title", "O campo e obrigatorio");
        $servidor_id->setSize("50%");
        $servidor_id->addValidation( TextFormat::set( "Servidor ID" ), new TRequiredValidator );

        $data_inicio->setProperty("title", "O campo e obrigatorio");
        $data_inicio->setSize("30%");
        $data_inicio->addValidation( TextFormat::set( "Data Inicio" ), new TRequiredValidator );

        

        $data_fim->setProperty("title", "O campo e obrigatorio");
        $data_fim->setSize("30%");

        $motivo->setProperty("title", "O campo e obrigatorio");
        $motivo->setSize("30%");
        $motivo->addValidation( TextFormat::set( "Motivo" ), new TRequiredValidator );


        


        $this->form->addFields( [ $id ] );
        $this->form->addFields([new TLabel("Servidor ID: $redstar")], [$servidor_id]);
        $this->form->addFields([new TLabel("Data Inicio: $redstar")], [$data_inicio]);
      
        $this->form->addFields([new TLabel("Data Fim: $redstar")], [$data_fim]);
        $this->form->addFields([new TLabel("Motivo: $redstar")], [$motivo]);
      

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

            $object = $this->form->getData("AfastamentoRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "AfastamentoList", "onReload" ] );

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

                TTransaction::open( "database" );

                $object = new AfastamentoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}