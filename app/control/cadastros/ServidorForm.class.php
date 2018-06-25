<?php

class ServidorForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Servidores" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_servidor" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $nome = new TEntry('nome');
        $matricula = new TEntry('matricula');
        $numero_conselho = new TEntry('numero_conselho');
        $email = new TEntry('email');
        $telefone = new TEntry('telefone');

        $regime = new TEntry('regime');

        $logradouro = new TEntry('logradouro');
        $numero = new TEntry('numero');
        $complemento = new TEntry('complemento');
        $bairro = new TEntry('bairro');
        $cidade = new TEntry('cidade');
        $uf = new TEntry('uf');
        $tipo_sanguineo = new TEntry('tipo_sanguineo');
        $escala_id        = new TDBCombo("escala_id", "database", "EscalaRecord", "id", "dia_semana_inicio");

        $cargo_id        = new TDBCombo("cargo_id", "database", "CargoRecord", "id", "descricao");
        $setor_id        = new TDBCombo("setor_id", "database", "SetorRecord", "id", "nomesetor");
        $hora_inicio_jornada = new TEntry('hora_inicio_jornada');
        $hora_fim_jornada = new TEntry('hora_fim_jornada');

        //$nomeprofissional->forceUpperCase();
        //$numeroconselho->setMask( "A!" );




        $nome->setSize("50%");
        $matricula->setSize("50%");
   $numero_conselho->setSize("50%");
   $email->setSize("50%");
           $telefone->setSize("50%");

   $regime->setSize("50%");
  

   $logradouro->setSize("50%");
   $numero->setSize("20%");
   $complemento->setSize("50%");
   $bairro->setSize("30%");
   $cidade->setSize("30%");
   $uf->setSize("20%");
   $tipo_sanguineo->setSize("20%");
    $escala_id->setSize("30%");
   $cargo_id->setSize("30%");
   $setor_id->setSize("30%");
   $hora_inicio_jornada->setSize("20%");
   $hora_fim_jornada->setSize("20%");

       
       
   $nome->addValidation('Nome' , new TRequiredValidator);
   $matricula->addValidation('Matricula' , new TRequiredValidator);
   $telefone->addValidation('Telefone' , new TRequiredValidator);
   $cargo_id->addValidation('Cargo' , new TRequiredValidator);
   $setor_id->addValidation('Setor' , new TRequiredValidator);
   $logradouro->addValidation('Logradouro' , new TRequiredValidator);
   $numero->addValidation('Numero' , new TRequiredValidator);
   $bairro->addValidation('Bairro' , new TRequiredValidator);
   $cidade->addValidation('Cidade' , new TRequiredValidator);
   $uf->addValidation('Uf' , new TRequiredValidator);
 

   $escala_id->setDefaultOption( "..::SELECIONE::.." );
   $cargo_id->setDefaultOption( "..::SELECIONE::.." );
   $setor_id->setDefaultOption( "..::SELECIONE::.." );


        $this->form->addFields([$id]);
        $this->form->addFields([new TLabel("Nome do Servidor: $redstar")], [$nome]);
        $this->form->addFields([new TLabel("Matrícula: $redstar")], [$matricula]);
        $this->form->addFields([new TLabel("Numero do Conselho: ")], [$numero_conselho]);
        $this->form->addFields([new TLabel("Email: ")], [$email]);
        $this->form->addFields([new TLabel("Telefone: $redstar")], [$telefone]);
        $this->form->addFields([new TLabel("Regime: ")], [$regime]);
        $this->form->addFields([new TLabel("Logradouro: $redstar")], [$logradouro]);
        $this->form->addFields([new TLabel("Número: $redstar")], [$numero]);
        $this->form->addFields([new TLabel("Complemento:")], [$complemento]);
        $this->form->addFields([new TLabel("Bairro: $redstar")], [$bairro]);
        $this->form->addFields([new TLabel("Cidade: $redstar")], [$cidade]);
        $this->form->addFields([new TLabel("uf: $redstar")], [$uf]);
        $this->form->addFields([new TLabel("Tipo Sanguíneo: ")], [$tipo_sanguineo]);

        $this->form->addFields([new TLabel("Escala: ")], [$escala_id]);
        $this->form->addFields([new TLabel("Cargo: ")], [$cargo_id]);
        $this->form->addFields([new TLabel("Setor: $redstar")], [$setor_id]);
        $this->form->addFields([new TLabel("Hora Inicio Jornada: ")], [$hora_inicio_jornada]);
        $this->form->addFields([new TLabel("Hora Fim Jornada: ")], [$hora_fim_jornada]);

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar", new TAction( [ "ServidorList", "onReload" ] ), "fa:table blue" );



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

                $object = $this->form->getData("ServidorRecord");
                $object->store();

            TTransaction::close();

            $action = new TAction( [ "ServidorList", "onReload" ] );

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

                $object = new ServidorRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
