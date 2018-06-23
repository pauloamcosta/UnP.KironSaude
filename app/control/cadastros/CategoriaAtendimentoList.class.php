<?php

class CategoriaAtendimentoList extends TPage
{
    // Definição do formulário
    private $form;
    // Definição do formulário
    private $datagrid;
    // Definição da paginação
    private $pageNavigation;
    // Variável de controle de carregamento da página
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_list_categoriaatendimento" );

        // Define o título.
        $this->form->setFormTitle("<b>Categorias de Atendimento</b>" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $opcao->setSize( "100%" );
        $opcao->setDefaultOption( "..::SELECIONE::.." );
        // Define a lista de opções de busca. 'nome' é o campo no banco, 'Nome' é o da exibição;
        $opcao->addItems( [ "nome" => "Nome" ] );

        $dados = new TEntry( "dados" );
        $dados->setSize( "100%" );
        $dados->setProperty( "title", "Informe os dados de acordo com a opção" );

        // Gera a estrutura do formulário de busca
        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        // Adiciona os botões de busca e cadastro
        $this->form->addAction( "Buscar categoria", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Nova categoria", new TAction( [ "CategoriaAtendimentoForm", "onEdit" ] ), "bs:plus-sign green" );

        // Adiciona as colunas na grade de consulta
        $column_nomecategoria = new TDataGridColumn( "nome", "Nome", "left" );

        // Define o botão de edição de registro
        $action_edit = new TDatagridTablesAction( [ "CategoriaAtendimentoForm", "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar Registro" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );

        // Define o botão de exclusão de registro
        $action_del = new TDatagridTablesAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar Registro" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );

        // Gera a estrutura do grid de consulta
        $this->datagrid = new TDatagridTables();
        $this->datagrid->addColumn( $column_nomecategoria );
        $this->datagrid->addAction( $action_edit );
        $this->datagrid->addAction( $action_del );
        $this->datagrid->createModel();

        // Define a paginação
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        // Gera o conteúdo a ser apresentado
        // Conteúdo: blocos com o formulário de busca e a grade de consulta
        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        parent::add( $container );
    }

    // Função para carregamento da página
    public function onReload( $param = NULL )
    {
        try {

            // Conexão com o banco
            TTransaction::open( "database" );

            // Nome do repositório (Record). No arquivo Record é informada a tabela do banco de dados.
            $repository = new TRepository( "CategoriaAtendimentoRecord" );

            // Define estrutura de busca e carregamento dos dados
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

    // Função para realização de consulta
    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                // Conexão com o banco
                TTransaction::open( "database" );

                // Nome do repositório (Record). No arquivo Record é informada a tabela do banco de dados.
                $repository = new TRepository( "CategoriaAtendimentoRecord" );

                // Define estrutura de busca e carregamento dos dados
                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                switch ( $data->opcao ) {

                    case "nomecategoria":
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

    // Função para confirmação de exclusão do registro
    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );

        }
    }

    // Função para exclusão do registro
    function Delete( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $object = new CategoriaAtendimentoRecord( $param[ "key" ] );
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
