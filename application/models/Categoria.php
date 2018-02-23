<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria extends MY_Model {

    // id do estado
    public $CodCategoria;

    // nome
    public $nome;

    // foto
    public $foto;

    // referencia
    public $ref;

    // entidade
    public $entity = 'Categoria';
    
    // tabela
    public $table = 'Categorias';

    // chave primaria
    public $primaryKey = 'CodCategoria';

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
    }
    
    public function setCod( $cod ) {
        $this->CodCategoria = $cod;
    }

    // nome
    public function setNome( $nome ) {
        $this->nome = $nome;
    }

    // foto
    public function setFoto( $foto ) {
        $this->foto = $foto;
    }

    /**
     * Seta o código de referência
     *
     */
    public function setRef( $ref ) {
        $this->ref = $ref;
        return $this;
    }
}

/* end of file */
