<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tablets extends MY_Controller {

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
        $this->load->finder( 'TabletsFinder' );

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
    private function _formularioTablets() {

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

        // faz a paginacao
		$this->TabletsFinder->clean()->grid()

		// seta os filtros
        ->addFilter( 'BasicCode', 'text' )
        ->addFilter( 'Nome', 'text', false, 't' )
		->filter()
		->order()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'tablets/alterar/'.$row[$key] ).'" class="margin btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
			echo '<a href="'.site_url( 'tablets/excluir/'.$row[$key] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';            
		})

        // seta as funcoes nas colunas
		->onApply( 'Foto', function( $row, $key ) {
            if( $row[$key] )
			    echo '<img src="'.$row[$key].'" style="width: 50px; height: 50px;">';
            else echo 'Sem Foto';
		})

		// renderiza o grid
		->render( site_url( 'tablets/index' ) );
		
        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'tablets/adicionar' ) );

        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'tablets/adicionar' ) )
        ->set( 'import_url', site_url( 'tablets/importar_planilha' ) )
        ->set( 'example_url', site_url( 'exemplos/tablets' ) )       
        ->set( 'export_url', site_url( 'tablets/exportar_planilha' ) );

		// seta o titulo da pagina
		$this->view->setTitle( 'Tablets - listagem' )->render( 'grid' );
    }

    /**
     * Exporta a planilha
     *
     * @return void
     */
    public function exportar_planilha() {

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=TabletsExportação".date( 'H:i d-m-Y', time() ).".xls" );

        // faz a paginacao
		$this->TabletsFinder->clean()->exportar()
        ->paginate( 1, 0, false, false )

		// renderiza o grid
		->render( site_url( 'tablets/index' ) );

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

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Adicionar tablet' )->render( 'forms/tablet' );
    }

   /**
    * alterar
    *
    * mostra o formulario de edicao
    *
    */
    public function alterar( $key ) {

        // carrega o cargo
        $tablet = $this->TabletsFinder->key( $key )->get( true );

        // verifica se o mesmo existe
        if ( !$tablet ) {
            redirect( 'tablets/index' );
            exit();
        }

        // salva na view
        $this->view->set( 'tablet', $tablet );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Alterar tablet' )->render( 'forms/tablet' );
    }

   /**
    * excluir
    *
    * exclui um item
    *
    */
    public function excluir( $key ) {
        $tablet = $this->TabletsFinder->key( $key )->get( true );
        $this->picture->delete( $tablet->foto );
        $tablet->delete();
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
            $tablet = $this->TabletsFinder->key( $this->input->post( 'cod' ) )->get( true );
        } else {

            // instancia um novo objeto grpo
            $tablet = $this->TabletsFinder->getTablet();            
            $tablet->setFoto( NULL );
        }

        $tablet->setBasicCode( $this->input->post( 'basiccode' ) );
        $tablet->setNome( $this->input->post( 'nome' ) );
        $tablet->setDescricao( $this->input->post( 'descricao' ) );
        $tablet->setPontos( $this->input->post( 'pontos' ) );
        $tablet->setVideo( $this->input->post( 'video' ) );
        $tablet->setCod( $this->input->post( 'cod' ) );

        if ( $file_name ) {
            $this->picture->delete( $tablet->foto );
            $tablet->setFoto( $file_name );
        }

        // verifica se o formulario é valido
        if ( !$this->_formularioTablets() ) {

            // seta os erros de validacao            
            $this->view->set( 'tablet', $tablet );
            $this->view->set( 'errors', validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Samsung - Adicionar tablet' )->render( 'forms/tablet' );
            return;
        }
        
        // verifica se o dado foi salvo
        if ( $tablet->save() ) {
            redirect( site_url( 'tablets/index' ) );
        }
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

        // Verifica o nome
        if ( !in_cell( $linha['nome'] ) ) {
            $this->import->insertLine( $row, 'NOME E OBRIGATORIO' );
            return;
        }

        // Verifica os pontos
        if ( !in_cell( $linha['pontos'] ) ) {
            $this->import->insertLine( $row, 'PONTOS E OBRIGATORIO' );
            return;
        }

        // Verifica os pontos
        if ( !is_numeric( $linha['pontos'] ) ) {
            $this->import->insertLine( $row, 'PONTOS PRECISA SER NUMERICO' );
            return;
        }

        // Verifica o basiccode
        if ( !in_cell( $linha['basiccode'] ) ) {
            $this->import->insertLine( $row, 'BASICCODE E OBRIGATORIO' );
            return;
        }

        // tenta carregar a loja pelo nome
        $tablet = $this->TabletsFinder->clean()->basicCode( $linha['basiccode'] )->get( true );

        // verifica se carregou
        if ( !$tablet ) $tablet = $this->TabletsFinder->getTablet();

        // preenche os dados
        $tablet->setBasicCode( $linha['basiccode'] );
        $tablet->setNome( $linha['nome'] );
        $tablet->setPontos( $linha['pontos'] );
        $tablet->setDescricao( null );
        $tablet->setVideo( null );
        $tablet->setFoto( 'sem-foto.jpg' );

        // Tenta salvar o tablet
        if ( !$tablet->save() ) {
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
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'tablets' ) ) {
                
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


