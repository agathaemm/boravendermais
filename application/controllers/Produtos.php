<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends MY_Controller {

    // indica se o controller é publico
	protected $public = false;

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
        
        // carrega o finder
        $this->load->finder( [ 'ProdutosFinder', 'CategoriasFinder' ] );

        // carrega a librarie de fotos
		$this->load->library( 'Picture' );
        
        // chama o modulo
        $this->view->module( 'navbar' )->module( 'aside' );
    }

   /**
    * _formularioEstados
    *
    * valida o formulario de estados
    *
    */
    private function _formularioProdutos() {

        // seta as regras
        $rules = [
            [
                'field' => 'basiccode',
                'label' => 'BasicCode',
                'rules' => 'required'
            ], [
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'required|min_length[3]|trim'
            ], [
                'field' => 'categoria',
                'label' => 'Categoria',
                'rules' => 'required'
            ], [
                'field' => 'pontos',
                'label' => 'Pontos',
                'rules' => 'required|is_numeric'
            ]
        ];

        // valida o formulário
        $this->form_validation->set_rules( $rules );
        return $this->form_validation->run();
    }

   /**
    * index
    *
    * mostra o grid de contadores
    *
    */
	public function index() {

        // carrega os categorias
        $categorias = $this->CategoriasFinder->filtro();

        // faz a paginacao
		$this->ProdutosFinder->clean()->grid()

		// seta os filtros
        ->addFilter( 'BasicCode', 'text' )
        ->addFilter( 'Nome', 'text', false, 'p' )
        ->addFilter( 'CodCategoria', 'select', $categorias, 'p' )
		->filter()
		->order()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'produtos/alterar/'.$row[$key] ).'" class="margin btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
			echo '<a href="'.site_url( 'produtos/excluir/'.$row[$key] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';            
		})

        // seta as funcoes nas colunas
		->onApply( 'Foto', function( $row, $key ) {
            if( $row[$key] )
			    echo '<img src="'.$row[$key].'" style="width: 50px; height: 50px;">';
            else echo 'Sem Foto';
		})

		// renderiza o grid
		->render( site_url( 'produtos/index' ) );
		
        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'produtos/adicionar' ) );

        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'produtos/adicionar' ) )
        ->set( 'import_url', site_url( 'produtos/importar_planilha' ) )
        ->set( 'example_url', site_url( 'exemplos/produtos' ) )       
        ->set( 'export_url', site_url( 'produtos/exportar_planilha' ) );

		// seta o titulo da pagina
		$this->view->setTitle( 'Produtos - listagem' )->render( 'grid' );
    }

    /**
     * Exporta a planilha
     *
     * @return void
     */
    public function exportar_planilha() {

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=LojasExportação".date( 'H:i d-m-Y', time() ).".xls" );

        // faz a paginacao
		$this->ProdutosFinder->clean()->exportar()
        ->paginate( 1, 0, false, false )

		// renderiza o grid
		->render( site_url( 'produtos/index' ) );

		// seta o titulo da pagina
		$this->view->component( 'table' );
    }

   /**
    * adicionar
    *
    * mostra o formulario de adicao
    *
    */
    public function adicionar() {

        // carrega os categorias
        $categorias = $this->CategoriasFinder->get();
        $this->view->set( 'categorias', $categorias );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Adicionar produto' )->render( 'forms/produto' );
    }

   /**
    * alterar
    *
    * mostra o formulario de edicao
    *
    */
    public function alterar( $key ) {

        // carrega os categorias
        $categorias = $this->CategoriasFinder->get();
        $this->view->set( 'categorias', $categorias );

        // carrega o cargo
        $produto = $this->ProdutosFinder->key( $key )->get( true );

        // verifica se o mesmo existe
        if ( !$produto ) {
            redirect( 'produtos/index' );
            exit();
        }

        // salva na view
        $this->view->set( 'produto', $produto );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Alterar produto' )->render( 'forms/produto' );
    }

   /**
    * excluir
    *
    * exclui um item
    *
    */
    public function excluir( $key ) {
        $produto = $this->ProdutosFinder->key( $key )->get( true );
        $this->picture->delete( $produto->foto );
        $produto->delete();
        $this->index();
    }

   /**
    * salvar
    *
    * salva os dados
    *
    */
    public function salvar() {

        // faz o upload da imagem
        $file_name = $this->picture->upload( 'foto', [ 'square' => 200 ] );

        if ( $this->input->post( 'cod' ) ) {
            $produto = $this->ProdutosFinder->key( $this->input->post( 'cod' ) )->get( true );
        } else {

            // instancia um novo objeto grpo
            $produto = $this->ProdutosFinder->getProduto();            
            $produto->setFoto( NULL );
        }

        $produto->setBasicCode( $this->input->post( 'basiccode' ) );
        $produto->setNome( $this->input->post( 'nome' ) );
        $produto->setCategoria( $this->input->post( 'categoria' ) );
        $produto->setDescricao( $this->input->post( 'descricao' ) );
        $produto->setPontos( $this->input->post( 'pontos' ) );
        $produto->setVideo( $this->input->post( 'video' ) );
        $produto->setCod( $this->input->post( 'cod' ) );

        if ( $file_name ) {
            $this->picture->delete( $produto->foto );
            $produto->setFoto( $file_name );
        }

        // verifica se o formulario é valido
        if ( !$this->_formularioProdutos() ) {

            // carrega os categorias
            $categorias = $this->CategoriasFinder->get();
            $this->view->set( 'categorias', $categorias );

            // seta os erros de validacao            
            $this->view->set( 'produto', $produto );
            $this->view->set( 'errors', validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Samsung - Adicionar produto' )->render( 'forms/produto' );
            return;
        }

        // verifica se o dado foi salvo
        if ( $produto->save() ) {
            redirect( site_url( 'produtos/index' ) );
        }
    }
        
   /**
    * obter_produtos_categoria
    *
    * obtem os produtos de uma categoria
    *
    */
    public function obter_produtos_categoria( $CodCategoria ) {

        // carrega a categoria
        $categoria = $this->CategoriasFinder->key( $CodCategoria )->get( true );
        
        if ( !$categoria ) return $this->close();

        // carrega os produtos de uma categoria
        $produtos = $this->ProdutosFinder->clean()->porCategoria( $CodCategoria )->get();
        if ( count( $produtos ) == 0 ) {
            echo json_encode( [] );
            return;
        }

        // faz o mapeamento dos produtos
        $produtos = array_map( function( $produto ) {
            return  [ 
                        'value' => $produto->CodProduto, 
                        'label' => $produto->nome
                    ];
        }, $produtos );
        // volta o json
        echo json_encode( $produtos );
        return;
    }
    
   /**
    * verificaEntidade
    *
    * verifica se um entidade existe no banco
    *
    */
    public function verificaEntidade( $finder, $method, $dado, $nome, $planilha, $linha, $attr, $status ) {

        // carrega o finder de logs
        $this->load->finder( 'LogsFinder' );

        // verifica se nao esta vazio
        if ( in_cell( $dado ) ) {

            // carrega o finder
            $this->load->finder( $finder );

            // pega a entidade
            if ( $entidade = $this->$finder->clean()->$method( $dado )->get( true ) ) {
                return $entidade->$attr;
            } else {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( $planilha )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'O campo '.$nome.' com valor '.$dado.' nao esta gravado no banco - linha '.$linha )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( $status )
                ->save();

                // retorna falso
                return null;
            }
        } else {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( $planilha )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Nenhum '.$nome.' encontrado - linha '.$linha )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( $status )            
            ->save();

            // retorna falso
            return null;
        }
    }
    
   /**
    * importar_linha
    *
    * importa a linha
    *
    */
    public function importar_linha( $row, $num ) {
        $this->load->library( 'validation' );
        $linha = lower_case_keys( $row );
        
        // percorre todos os campos
        foreach( $linha as $chave => $coluna ) {
            $linha[$chave] = in_cell( $linha[$chave] ) ? $linha[$chave] : null;
        }

        // pega as entidades relacionaveis
        $linha['CodCategoria'] = $this->verificaEntidade( 'CategoriasFinder', 'nome', $linha['categoria'], 'Categorias', 'Produtos', $num, 'CodCategoria', 'I' );
        if ( !in_cell( $linha['CodCategoria'] ) ) {
            $linha['CodCategoria'] = $this->verificaEntidade( 'CategoriasFinder', 'byRefCode', $linha['categoria'], 'Categorias', 'Produtos', $num, 'CodCategoria', 'I' );
        }
        if ( !in_cell( $linha['CodCategoria'] ) ) {
            $this->import->insertLine( $row, 'NENHUMA CATEGORIA INFORMADA' );
            return;
        }

        // Verifica o nome
        if ( !in_cell( $linha['nome'] ) ) {
            $this->import->insertLine( $row, 'NOME E OBRIGATORIO' );
            return;
        }

        // Verifica o nome
        if ( !in_cell( $linha['pontos'] ) ) {
            $this->import->insertLine( $row, 'PONTOS E OBRIGATORIO' );
            return;
        }

        // Verifica o nome
        if ( !is_numeric( $linha['pontos'] ) ) {
            $this->import->insertLine( $row, 'PONTOS PRECISA SER NUMERICO' );
            return;
        }

        // Verifica o nome
        if ( !in_cell( $linha['basiccode'] ) ) {
            $this->import->insertLine( $row, 'BASICCODE E OBRIGATORIO' );
            return;
        }

        // tenta carregar a loja pelo nome
        $produto = $this->ProdutosFinder->clean()->basicCode( $linha['basiccode'] )->get( true );

        // verifica se carregou
        if ( !$produto ) $produto = $this->ProdutosFinder->getProduto();

        // preenche os dados
        $produto->setBasicCode( $linha['basiccode'] );
        $produto->setNome( $linha['nome'] );
        $produto->setCategoria( $linha['CodCategoria'] );
        $produto->setPontos( $linha['pontos'] );
        $produto->setDescricao( null );
        $produto->setVideo( null );
        $produto->setFoto( 'sem-foto.jpg' );

        // Tenta salvar o produto
        if ( !$produto->save() ) {
            $this->import->insertLine( $row, 'NENHUM ERRO IDENTIFICADO' );
            return;
        }
    }
    
    /**
     * Faz a importação da planilha
     *
     * @return void
     */
    public function importar_planilha() {

        // importa a planilha
        $this->load->library( 'Planilhas' );

        // faz o upload da planilha
        $planilha = $this->planilhas->upload();

        // tenta fazer o upload
        if ( !$planilha ) {

            // seta os erros
            $this->view->set( 'errors', $this->planilhas->errors );
        } else {

            // Verifica se o header é válido
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'produtos' ) ) {
                
                // seta os erros
                array_unshift( $missing, '<br><b>Os campos faltantes são:</b>' );
                array_unshift( $missing, 'Caso tenha duvidas de como importar, clique no botão <b>Visualizar exemplo de Importação</b> logo abaixo.' );
                array_unshift( $missing, 'A planilha que você tentou importar não possui todos os campos requeridos.' );
                $this->view->set( 'errors', $missing );
            } else {
                $this->import->cleanTable();
                $this->planilhas->apply( function( $a, $b ) {
                    $this->importar_linha( $a , $b );
                });
                if ( $this->import->hasNoImportedLines() ) {
                    // seta os erros
                    $this->view->set( 'warnings', [
                        'A importação foi finalizada, porém algumas linhas não foram importadas.',
                        '<b>Para fazer o download de todas as linhas não importadas clique no link abaixo:</b>',
                        '<a href="'.site_url( 'exemplos/export_import_errors').'" target="blank">Clique aqui para baixar a planilha</a>'
                    ]);
                } else {
                    flash( 'success', 'Todas as linhas foram importadas com sucesso!' );
                }
            }

            $planilha->excluir();
        }

        // Abre a index
        $this->index();
    }
}

// End of file


