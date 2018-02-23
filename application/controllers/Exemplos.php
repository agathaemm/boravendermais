<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Exemplos extends MY_Controller {

    /**
     * Inidica se o controller é public
     * 
     */
    public $public = false;

    /**
     * Método construtor
     *
     */
    public function __construct() {
        parent::__construct();
        
        // chama o modulo
        $this->view->setTitle( 'Exemplo de importação' )->module( 'navbar' )->module( 'aside' );
    }

    /**
     * Renderiza a view
     *
     * @return void
     */
    public function index( $data ) {
        $this->view->set( 'data', $data );
        $this->view->render( 'exemplos' );
    }

    /**
     * Exporta um exemplo de planilha
     *
     * @param [type] $ref
     * @return void
     */
    public function export( $ref ) {
        if ( method_exists( $this, $ref ) ) {
            header("Content-type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=exemplo_importacao_".$ref.".xls" );
            $data = $this->$ref( true );
            $this->view->set( 'data', $data );
            $this->view->render( 'exemplos_export' );
        }
    }

    /**
     * Mostra o exemplo de importação para clusters
     *
     * @return void
     */
    public function clusters( $return = false ) {
        $data = $this->schema->get( 'clusters' );
        $this->view->set( 'entidade', 'Clusters' );
        $this->view->set( 'export_method', 'clusters' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para redes
     *
     * @return void
     */
    public function redes( $return = false ) {
        $data = $this->schema->get( 'redes' );
        $this->view->set( 'entidade', 'Redes' );
        $this->view->set( 'export_method', 'redes' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para lojas
     *
     * @return void
     */
    public function lojas( $return = false ) {
        $data = $this->schema->get( 'lojas' );
        $this->view->set( 'entidade', 'Lojas' );
        $this->view->set( 'export_method', 'lojas' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para funcionarios
     *
     * @return void
     */
    public function funcionarios( $return = false ) {
        $data = $this->schema->get( 'funcionarios' );
        $this->view->set( 'entidade', 'Funcionarios' );
        $this->view->set( 'export_method', 'funcionarios' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para produtos
     *
     * @return void
     */
    public function produtos( $return = false ) {
        $data = $this->schema->get( 'produtos' );
        $this->view->set( 'entidade', 'Produtos' );
        $this->view->set( 'export_method', 'produtos' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para tablets
     *
     * @return void
     */
    public function tablets( $return = false ) {
        $data = $this->schema->get( 'tablets' );
        $this->view->set( 'entidade', 'Tablets' );
        $this->view->set( 'export_method', 'tablets' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para vendas tablet
     *
     * @return void
     */
    public function vendas_tablet( $return = false ) {
        $data = $this->schema->get( 'vendas_tablet' );
        $this->view->set( 'entidade', 'Vendas_tablet' );
        $this->view->set( 'export_method', 'vendas_tablet' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Mostra o exemplo de importação para vendas
     *
     * @return void
     */
    public function vendas( $return = false ) {
        $data = $this->schema->get( 'vendas' );
        $this->view->set( 'entidade', 'Vendas' );
        $this->view->set( 'export_method', 'vendas' );

        // Verifica o tipo de retorno
        if ( !$return ) { 
            $this->index( $data );
        } else return $data;
    }

    /**
     * Exporta a planilha com os erros de importação
     *
     * @return void
     */
    public function export_import_errors() {

        // Carrega as linhas
        $lines = $this->import->getLines();

        // Se não existirem linhas, fecha a janela
        if ( count( $lines ) == 0 ) echo '<script>window.close()</script>';

        // Seta os headers
        header("Content-type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=erros_importacao_".date( 'Y_m_d_H_i_s', time() ).".xls" );    
        
        // Renderiza a view
        $this->view->set( 'lines', $lines );
        $this->view->set( 'header', array_keys( json_decode( $lines[0]['line'], true ) ) );
        $this->view->render( 'errors_export' );
    }
}

// End of file
