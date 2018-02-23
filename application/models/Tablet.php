<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tablet extends MY_Model {

    // id do estado
    public $CodTablet;
    
    // basiccode
    public $basiccode;

    // nome
    public $nome;

    // descricao
    public $descricao;

    // foto
    public $foto;

    // pontos
    public $pontos;

    // video
    public $video;

    // entidade
    public $entity = 'Tablet';
    
    // tabela
    public $table = 'Tablets';

    // chave primaria
    public $primaryKey = 'CodTablet';

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
        $this->CodTablet = $cod;
    }

    // basiccode
    public function setBasicCode( $basiccode ) {
        $this->basiccode = $basiccode;
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

    // pontos
    public function setPontos( $pontos ) {
        $this->pontos = $pontos;
    }

    // video
    public function setVideo( $video ) {
        $this->video = $video;
    }
}

/* end of file */
