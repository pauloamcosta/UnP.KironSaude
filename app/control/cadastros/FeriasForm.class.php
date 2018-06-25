<?php

class FeriasForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Entrada de servidor em Internação" );

        parent::setSize( 0.650, 0.700 );

        $redstar = '<font color="red"><b>*</b></font>';

        $id           = new THidden( "id" );


        $servidor     = new TDBUniqueSearch("servidor_id", "database", "ServidorRecord", "id", "nome");
        $servidor->setSize("100%");
        $servidor->addValidation( TextFormat::set( "servidor" ), new TRequiredValidator );
        $servidor->setMinLength(3);

        $data_inicio = new TDateTime('data_inicio');
        $data_inicio->setSize("25%");
        $data_inicio->setMask('dd/mm/yyyy hh:ii');
        $data_inicio->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_inicio->setValue(date('Y-m-d H:i'));

        $quantidade_dias = new TEntry("quantidade_dias");
        $quantidade_dias->setProperty("title", "O campo e obrigatorio");
        $quantidade_dias->setSize("38%");


        $data_fim = new TDateTime('data_fim');
        $data_fim->setSize("25%");
        $data_fim->setMask('dd/mm/yyyy hh:ii');
        $data_fim->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_fim->setValue(date('Y-m-d H:i'));
    
        

        $this->form = new BootstrapFormBuilder( "form_acompanhante" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $this->form->addFields( [ $id ] );
        $this->form->addFields([new TLabel("servidor: $redstar")], [$servidor]);
        $this->form->addFields([new TLabel("Data Inicio: $redstar")], [$data_inicio]);
        $this->form->addFields([new TLabel("Quantidade Dias: $redstar")], [$quantidade_dias]);
        $this->form->addFields([new TLabel("DataFim: $redstar")], [$data_fim]);
        
        
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

            $object = $this->form->getData("FeriasRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "FeriasList", "onReload" ] );

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
                parent::setTitle( "Edição de Internação" );

                TTransaction::open( "database" );

                $object = new FeriasRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}