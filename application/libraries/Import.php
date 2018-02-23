<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** 
* Classe de importacao de planilhas
*
*/
class Import {

    // instancia do ci
    public $ci;

    /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {

        // pega a instancia do codeigniter
        $this->ci =& get_instance();
    }

    /**
     * Esvazia a tabela de importaçao
     *
     * @return void
     */
    public function cleanTable() {
        $this->ci->db->query( 'DELETE FROM imports' );
        return $this;
    }

    /**
     * Insere uma linha na tabela de importação
     *
     * @param [type] $line
     * @return void
     */
    public function insertLine( $line, $motivo = null ) {
        if ( safe_json_encode( $line ) ) {
            $this->ci->db->insert( 'imports', [ 'line' => safe_json_encode( $line ), 'motivo' => $motivo ] );
        } else {
            debug( $line, false );
        }
    }

    /**
     * Pega todas
     *
     * @return void
     */
    public function getLines() {
        $result = $this->ci->db->query( "SELECT * FROM imports" );
        return $result->result_array();
    }

    /**
     * Indica se existem linhas que não foram importadas
     *
     * @return boolean
     */
    public function hasNoImportedLines() {
        $query = $this->ci->db->query('SELECT * FROM imports');
        return $query->num_rows() > 0 ? true : false;
    }
}

// End of file
