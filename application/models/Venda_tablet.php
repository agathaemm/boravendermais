<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Venda_tablet extends MY_Model {

    // id do estado
    public $CodVendaTablet;

    // funcionario
    public $funcionario;

    // tablet
    public $tablet;

    // quantidade
    public $quantidade;

    // data
    public $data;

    // pontos
    public $pontos;

    // loja
    public $loja;

    // valor da venda
    public $valor;

    // entidade
    public $entity = 'Venda_tablet';
    
    // tabela
    public $table = 'Vendas_tablet';

    // chave primaria
    public $primaryKey = 'CodVendaTablet';

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
        $this->CodVendaTablet = $cod;
    }

    // funcionario
    public function setFuncionario( $funcionario ) {
        $this->funcionario = $funcionario;
    }

    // quantidade
    public function setQuantidade( $quantidade ) {
        $this->quantidade = $quantidade;
    }

    // tablet
    public function setTablet( $tablet ) {
        $this->tablet = $tablet;
    }

    // data
    public function setData( $data ) {
        $this->data = $data;
    }

    // pontos
    public function setPontos( $pontos ) {
        $this->pontos = $pontos;
    }

    // loja
    public function setLoja( $loja ) {
        $this->loja = $loja;
    }

    // seta o valor
    public function setValor( $valor ) {
        $this->valor = $valor;
        return $this;
    }
}

/* end of file */
