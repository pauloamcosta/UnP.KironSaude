<?php

class InternacaoForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Entrada de Paciente em Internação" );

        parent::setSize( 0.650, 0.700 );

        $redstar = '<font color="red"><b>*</b></font>';

        $id           = new THidden( "id" );

        $situacao     = new THidden( "situacao" );
        $situacao->setValue(1);

        $paciente     = new TDBUniqueSearch("paciente_id", "database", "VwPacienteConsultaRecord", "id", "dadospaciente");
        $paciente->setSize("100%");
        $paciente->addValidation( TextFormat::set( "Paciente" ), new TRequiredValidator );
        $paciente->setMinLength(3);

        $data_entrada = new TDateTime('data_entrada');
        $data_entrada->setSize("25%");
        $data_entrada->setMask('dd/mm/yyyy hh:ii');
        $data_entrada->setDatabaseMask('yyyy-mm-dd hh:ii');
        $data_entrada->setValue(date('Y-m-d H:i'));

        $servidor     = new TDBUniqueSearch("servidor_id", "database", "VwServidorConsultaRecord", "id", "dadosservidor");
        $servidor->setSize("100%");
        $servidor->setMinLength(3);
        $servidor->addValidation( TextFormat::set( "Servidor" ), new TRequiredValidator );

        $leito        = new TDBCombo("leito_id", "database", "VwLeitosDisponiveisRecord", "id", "nomeleito");
        $leito->setSize("35%");
        $leito->setDefaultOption( "..::SELECIONE::.." );
        $leito->addValidation( TextFormat::set( "Leito" ), new TRequiredValidator );

        $acompanhante = new TDBUniqueSearch("acompanhante_id", "database", "VwAcompanhanteConsultaRecord", "id", "dadosacompanhante");
        $acompanhante->setSize("100%");
        $acompanhante->setMinLength(3);

        $this->form = new BootstrapFormBuilder( "form_acompanhante" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $this->form->addFields( [ $id ] );
        $this->form->addFields( [ $situacao ] );
        $this->form->addFields([new TLabel("Paciente: $redstar")], [$paciente]);
        $this->form->addFields([new TLabel("Internado em: $redstar")], [$data_entrada]);
        $this->form->addFields([new TLabel("Médico: $redstar")], [$servidor]);
        $this->form->addFields([new TLabel("Leito: $redstar")], [$leito]);
        $this->form->addFields([new TLabel("Acompanhante: ")], [$acompanhante]);
        
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

            $object = $this->form->getData("InternacaoRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "InternacaoList", "onReload" ] );

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

                $object = new InternacaoRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}