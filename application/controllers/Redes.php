<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Redes extends MY_Controller {

    /**
     * Indica se o controller é publico
     *
     * @var boolean
     */
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
        $this->load->finder( [ 'RedesFinder', 'ClustersFinder' ] );
        
        // chama o modulo
        $this->view->module( 'navbar' )->module( 'aside' );
    }

    /**
     * Formulário de Redes
     *
     */
    private function _formularioRede() {

        // seta as regras
        $rules = [
            [
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'max_length[255]|required'
            ], [
                'field' => 'ref',
                'label' => 'Referência',
                'rules' => 'max_length[255]|required'
            ]
        ];

        // valida o formulário
        $this->form_validation->set_rules( $rules );
        return $this->form_validation->run();
    }

    /**
     * Mostra o grid de redes
     *
     */
	public function index() {

        // faz a paginacao
		$this->RedesFinder->grid()

		// seta os filtros
		->order()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'redes/alterar/'.$row[$key] ).'" class="margin btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
			echo '<a href="'.site_url( 'redes/excluir/'.$row[$key] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';                
		})

		// renderiza o grid
		->render( site_url( 'redes/index' ) );
		
        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'redes/adicionar' ) )
        ->set( 'import_url', site_url( 'redes/importar_planilha' ) )
        ->set( 'example_url', site_url( 'exemplos/redes' ) )
        ->set( 'export_url', site_url( 'redes/exportar_planilha' ) );

		// seta o titulo da pagina
		$this->view->setTitle( 'Redes - listagem' )->render( 'grid' );
    }

    /**
     * Mostra o formulário de adição
     *
     */
    public function adicionar() {
        
        // carrega o jquery mask
        $this->view->module( 'jquery-mask' );

        // Carrega os clusters
        $clusters = $this->ClustersFinder->get();
        $this->view->set( 'clusters', $clusters );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Adicionar redes' )->render( 'forms/rede' );
    }

    /**
     * Mostra o formulário de edição
     *
     */
    public function alterar( $key ) {

        // carrega o cargo
        $rede = $this->RedesFinder->key( $key )->get( true );

        // Carrega os clusters
        $clusters = $this->ClustersFinder->get();
        $this->view->set( 'clusters', $clusters );

        // salva na view
        $this->view->set( 'rede', $rede );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Alterar rede' )->render( 'forms/rede' );
    }

    /**
     * Exclui uma rede
     *
     */
    public function excluir( $key ) {

        // carrega a rede
        $rede = $this->RedesFinder->key( $key )->get( true );
        if ( $rede && $rede->delete() ) {
            flash( 'success', 'Item deletado com sucesso!' );
        } else {
            flash( 'error', 'Erro ao deletar o item!' );
        }

        // redireciona
        redirect( site_url( 'redes/index' ) );
    }

    /**
     * Salva os dados
     *
     */
    public function salvar() {        
        
        // instancia um novo objeto grupo
        $rede = $this->RedesFinder->getRede();        
        
        // Seta o código, se existir
        $rede->setCod( $this->input->post( 'cod' ) )
        ->setCluster( $this->input->post( 'cluster' ) )
        ->setNome( $this->input->post( 'nome' ) )
        ->setRef( $this->input->post( 'ref' ) );

        // verifica se o formulario é valido
        if ( !$this->_formularioRede() ) {

            // seta os erros de validacao            
            $this->view->set( 'rede', $rede );
            $this->view->set( 'errors', validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Samsung - Adicionar rede' )->render( 'forms/rede' );
            return;
        }

        // Tenta salvar a rede
        if ( $rede->save() ) {
            flash( 'success', 'Item salvo com sucesso!' );
        } else {
            flash( 'error', 'Erro ao salvar o item!' );
        }

        // redireciona
        redirect( site_url( 'redes/index' ) );
    }

    /**
    * obter_cidades_estado
    *
    * obtem as cidades de um estado
    *
    */
    public function obter_redes_cluster( $CodCluster ) {

        // carrega o estado
        $cluster = $this->ClustersFinder->key( $CodCluster )->get( true );
        if ( !$cluster ) { echo json_encode( [] ); return; };

        // carrega as cidades do estado
        $redes = $this->RedesFinder->clean()->porCluster( $CodCluster )->get();
        if ( count( $redes ) == 0 ) {
            echo json_encode( [] );
            return;
        }

        // faz o mapeamento das cidades
        $redes = array_map( function( $rede ) {
            return  [ 
                        'value' => $rede->CodRede, 
                        'label' => $rede->nome
                    ];
        }, $redes );

        // volta o json
        echo json_encode( $redes );
        return;
    }

    /**
     * Faz a importação de uma linha da planilha para o banco de dados
     *
     * @param [type] $linha
     * @return void
     */
    private function __importarLinha( $linha, $num ) {
        $minLinha = lower_case_keys( $linha );

        // Verifica se existe um cluster
        $toValidate = [
            'nome'    => $minLinha['nome'],
            'ref'     => $minLinha['referencia']
        ];

        // Seta os dados do form
        $this->form_validation->set_data( $toValidate );

        // Verifica se o formulário é válido
        $result = $this->_formularioRede();
        if ( $result ) {
            
            // Seta os dados
            $rede = $this->RedesFinder->getRede();
            $rede->setNome( $toValidate['nome'] );
            $rede->setRef( $toValidate['ref'] );

            // Salva o cluster
            if ( !$rede->save() ) $this->import->insertLine( $linha );
        } else $this->import->insertLine( $linha );
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
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'redes' ) ) {
                
                // seta os erros
                array_unshift( $missing, '<br><b>Os campos faltantes são:</b>' );
                array_unshift( $missing, 'Caso tenha duvidas de como importar, clique no botão <b>Visualizar exemplo de Importação</b> logo abaixo.' );
                array_unshift( $missing, 'A planilha que você tentou importar não possui todos os campos requeridos.' );
                $this->view->set( 'errors', $missing );
            } else {
                $this->import->cleanTable();
                $this->planilhas->apply( function( $a, $b ) {
                    $this->__importarLinha( $a , $b );
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
