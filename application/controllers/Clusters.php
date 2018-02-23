<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Clusters extends MY_Controller {

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
        $this->load->finder( [ 'ClustersFinder' ] );
        
        // chama o modulo
        $this->view->module( 'navbar' )->module( 'aside' );
    }

   /**
    * _formularioCluster
    *
    * valida o formulario de estados
    *
    */
    private function _formularioCluster() {

        // seta as regras
        $rules = [
            [
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'required|min_length[3]|trim'
            ], [
                'field' => 'ref',
                'label' => 'Referência',
                'rules' => 'required|trim'
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
		$this->ClustersFinder->grid()

		// seta os filtros
        ->addFilter( 'Nome', 'text' )
		->filter()
		->order()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'clusters/alterar/'.$row['Código'] ).'" class="margin btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
			echo '<a href="'.site_url( 'clusters/excluir/'.$row['Código'] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';            
		})

		// renderiza o grid
		->render( site_url( 'clusters/index' ) );
		
        // seta a url para adiciona
        $this->view
        ->set( 'add_url', site_url( 'clusters/adicionar' ) )
        ->set( 'example_url', site_url( 'exemplos/clusters' ) )
        ->set( 'import_url', site_url( 'clusters/importar_planilha' ) );

		// seta o titulo da pagina
		$this->view->setTitle( 'Clusters - Listagem' )->render( 'grid' );
    }

   /**
    * adicionar
    *
    * mostra o formulario de adicao
    *
    */
    public function adicionar() {

        // carrega a view de adicionar
        $this->view->setTitle( 'Adicionar cluster' )->render( 'forms/cluster' );
    }

   /**
    * alterar
    *
    * mostra o formulario de edicao
    *
    */
    public function alterar( $key ) {

        // carrega o cargo
        $cluster = $this->ClustersFinder->key( $key )->get( true );

        // verifica se o mesmo existe
        if ( !$cluster ) {
            redirect( 'clusters/index' );
            exit();
        }

        // salva na view
        $this->view->set( 'cluster', $cluster );

        // carrega a view de adicionar
        $this->view->setTitle( 'Alterar cluster' )->render( 'forms/cluster' );
    }

   /**
    * excluir
    *
    * exclui um item
    *
    */
    public function excluir( $key ) {
        $cluster = $this->ClustersFinder->getCluster();
        $cluster->setCod( $key );

        // Tenta deletar
        if ( $cluster->delete() ) {
            flash( 'success', 'Item deletado com sucesso!' );
        } else flash( 'error', 'Erro ao deletar o item!' );
        
        // Redireciona para a listagem
        redirect( site_url( 'clusters' ) );
    }

   /**
    * salvar
    *
    * salva os dados
    *
    */
    public function salvar() {

        // instancia um novo objeto grupo
        $cluster = $this->ClustersFinder->getCluster();
        $cluster->setNome( $this->input->post( 'nome' ) );
        $cluster->setCod( $this->input->post( 'cod' ) );
        $cluster->setRef( $this->input->post( 'ref' ) );

        // verifica se o formulario é valido
        if ( !$this->_formularioCluster() ) {

            // seta os erros de validacao            
            $this->view->set( 'cluster', $cluster );
            $this->view->set( 'errors', validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Adicionar cluster' )->render( 'forms/cluster' );
            return;
        }

        // verifica se o dado foi salvo
        if ( $cluster->save() ) {
            flash( 'success', 'Item salvo com sucesso!' );
        } else {
            flash( 'error', 'Erro ao salvar o item!' );
        }

        redirect( site_url( 'clusters' ) );
    }

    /**
     * Faz a importação de uma linha da planilha para o banco de dados
     *
     * @param [type] $linha
     * @return void
     */
    private function __importarLinha( $linha, $num ) {
        $minLinha = lower_case_keys( $linha );
        $toValidate = [
            'nome' => $minLinha['nome'],
            'ref'  => $minLinha['referencia']
        ];

        // Seta os dados do form
        $this->form_validation->set_data( $toValidate );

        // Verifica se o formulário é válido
        $result = $this->_formularioCluster();
        if ( $result ) {
            
            // Seta os dados
            $cluster = $this->ClustersFinder->getCluster();
            $cluster->setNome( $toValidate['nome'] );
            $cluster->setRef( $toValidate['ref'] );

            // Salva o cluster
            if ( !$cluster->save() ) $this->import->insertLine( $linha );
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
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'clusters' ) ) {
                
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

