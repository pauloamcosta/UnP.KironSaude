<?php

class InternacaoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_list_internacao" );

        $this->form->setFormTitle("<b>Internações</b>" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $opcao->setSize( "100%" );
        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $opcao->addItems([
            "paciente" => "Paciente"
        ]);

        $dados = new TEntry( "dados" );
        $dados->setSize( "100%" );
        $dados->placeholder = "Informe os dados de acordo com a opção de busca informada";

        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar internação", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Nova internação", new TAction( [ "InternacaoForm", "onEdit" ] ), "bs:plus-sign green" );

        $column_paciente     = new TDataGridColumn( "paciente", "Paciente", "left" );
        $column_data_entrada = new TDataGridColumn( "data_entrada", "Internado em", "left" );
        $column_servidor     = new TDataGridColumn( "servidor", "Médico", "left" );
        $column_leito        = new TDataGridColumn( "leito", "Leito", "left" );
        $column_acompanhante = new TDataGridColumn( "acompanhante", "Acompanhante", "left" );

        $action_edit = new TDatagridTablesAction( [ "InternacaoForm", "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar Registro" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );

        $action_del = new TDatagridTablesAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar Registro" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );

        $this->datagrid = new TDatagridTables();
        $this->datagrid->addColumn( $column_paciente );
        $this->datagrid->addColumn( $column_data_entrada );
        $this->datagrid->addColumn( $column_servidor );
        $this->datagrid->addColumn( $column_leito );
        $this->datagrid->addColumn( $column_acompanhante );
        $this->datagrid->addAction( $action_edit );
        $this->datagrid->addAction( $action_del );
        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        parent::add( $container );
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "VwInternacaoRecord" );

            if ( empty( $param[ "order" ] ) ) {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();

            if ( !empty( $objects ) ) {
                foreach ( $objects as $object ) {
                    $this->datagrid->addItem( $object );
                }
            }

            $criteria->resetProperties();

            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "VwInternacaoRecord" );

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                switch ( $data->opcao ) {

                    case "nome":
                    case "cpf":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                        break;

                    default:
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                        break;

                }

                $objects = $repository->load( $criteria, FALSE );

                $this->datagrid->clear();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        $this->datagrid->addItem( $object );
                    }
                } else {
                  new TMessage( "error", "Não há dados cadastrados!" );
                }

                $criteria->resetProperties();

                $count = $repository->count( $criteria );

                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );

                TTransaction::close();

                $this->form->setData( $data );

                $this->loaded = true;

            } else {

                $this->onReload();

                $this->form->setData( $data );

                new TMessage( "error", "Selecione uma opcao e informe os dados da busca corretamente!" );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $data );

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );

        }
    }

    function Delete( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $object = new InternacaoRecord( $param[ "key" ] );
            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage( "info", "Registro apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }
}
