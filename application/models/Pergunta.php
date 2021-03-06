<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pergunta extends MY_Model {

    // id do estado
    public $CodPergunta;

    // texto
    public $texto;

    // pontos
    public $pontos;

    // responsta
    public $resposta;

    // questionario
    public $questionario;

    // alternativa 1
    public $alternativa1;

    // alternativa 2
    public $alternativa2;

    // alternativa 3
    public $alternativa3;

    // alternativa 4
    public $alternativa4;

    // entidade
    public $entity = 'Pergunta';
    
    // tabela
    public $table = 'Perguntas';

    // chave primaria
    public $primaryKey = 'CodPergunta';

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
    }
    
    // codigo
    public function setCod( $cod ) {
        $this->CodPergunta = $cod;
        return $this;
    }

    // resposta
    public function setResposta( $resposta ) {
        $this->resposta = $resposta;
        return $this;        
    }

    // texto
    public function setTexto( $texto ) {
        $this->texto = $texto;
        return $this;        
    }

    // pontos
    public function setPontos( $pontos ) {
        $this->pontos = $pontos;
        return $this;        
    }

    // questionario
    public function setQuestionario( $questionario ) {
        $this->questionario = $questionario;
        return $this;
    }

    // alternativa1
    public function setAlternativa1( $alternativa1 ) {
        $this->alternativa1 = $alternativa1;
        return $this;
    }

    // alternativa2
    public function setAlternativa2( $alternativa2 ) {
        $this->alternativa2 = $alternativa2;
        return $this;
    }

    // alternativa3
    public function setAlternativa3( $alternativa3 ) {
        $this->alternativa3 = $alternativa3;
        return $this;
    }

    // alternativa4
    public function setAlternativa4( $alternativa4 ) {
        $this->alternativa4 = $alternativa4;
        return $this;
    }

    // verifica se ja foi respondida
    public function respondida( $func ) {

        // prepara a busca
        $this->db->from( $this->table.' p' )
        ->select( 'p.CodPergunta, r.*' )
        ->join( "Respostas r", "r.CodPergunta = p.CodPergunta" )
        ->where( "r.CodPergunta = $this->CodPergunta AND r.CodUsuario = $func " );

        // faz a busca
        $busca = $this->db->get();

        // volta o resultado
        return ( $busca->num_rows() > 0 ) ? $busca->result_array()[0] : false;
    }

    // verifica se esta correta
    public function correta( $func ) {

        // pega a resposta
        $resposta = $this->respondida( $func );

        // volta se esta correta
        return ( $resposta['Alternativa'] == $this->resposta ) ? true : false;
    }
}

/* end of file */
