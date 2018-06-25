<?php

class EscalaForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Escala" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_escala" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                   = new THidden( "id" );
        $dia_semana_inicio = new TEntry("dia_semana_inicio");
        $hora_entrada = new TEntry("hora_entrada");
        $horas_de_trabalho = new TEntry("horas_de_trabalho");
        $horas_de_folga = new TEntry("horas_de_folga");


        $dia_semana_inicio->setProperty("title", "O campo e obrigatorio");
        $dia_semana_inicio->setSize("38%");
        $dia_semana_inicio->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $hora_entrada->setProperty("title", "O campo e obrigatorio");
        $hora_entrada->setSize("38%");
        $hora_entrada->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $horas_de_trabalho->setProperty("title", "O campo e obrigatorio");
        $horas_de_trabalho->setSize("38%");
        $horas_de_trabalho->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $horas_de_folga->setProperty("title", "O campo e obrigatorio");
        $horas_de_folga->setSize("38%");
        $horas_de_folga->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Dia inicio semana: $redstar")], [$dia_semana_inicio]);
        $this->form->addFields([new TLabel("Hora de entrada: $redstar")], [$hora_entrada]);

        $this->form->addFields([new TLabel("Horas de trabalho: $redstar")], [$horas_de_trabalho]);

        $this->form->addFields([new TLabel("Horas de folga: $redstar")], [$horas_de_folga]);

        $this->form->addFields( [ $id ] );

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
                $object = $this->form->getData("EscalaRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "EscalaList", "onReload" ] );

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
                    $object = new CargoRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
