<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Treinamento extends MY_Model {

    // id do estado
    public $CodTreinamento;

    // nome
    public $nome;

    // descricao
    public $descricao;

    // foto
    public $foto;

    // video
    public $video;

    // questionario
    public $questionario;

    // entidade
    public $entity = 'Treinamento';
    
    // tabela
    public $table = 'Treinamentos';

    // chave primaria
    public $primaryKey = 'CodTreinamento';

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
        $this->CodTreinamento = $cod;
    }

    // nome
    public function setNome( $nome ) {
        $this->nome = $nome;
    }

    // descricao
    public function setDescricao( $descricao ) {
        $this->descricao = $descricao;
    }

    // foto
    public function setFoto( $foto ) {
        $this->foto = $foto;
    }

    // video
    public function setVideo( $video ) {
        $this->video = $video;
    }

    /**
     * Seta o quesitonário do treinamento
     *
     * @param [type] $questionario
     * @return void
     */
    public function setQuestionario( $questionario ) {
        $this->questionario = $questionario;
        return $this;
    }
}

/* end of file */
