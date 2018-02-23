<?php

require 'application/models/Venda_tablet.php';

class VendasTabletFinder extends MY_Model {

    // entidade
    public $entity = 'Venda_tablet';

    // tabela
    public $table = 'Vendas_tablet';

    // chave primaria
    public $primaryKey = 'CodVendaTablet';

    // labels
    public $labels = [
        'f.CodFuncionario' => 'f.CodFuncionario',
        'CodTablet'     => 'CodTablet',
        'Funcionario'    => 'CPF'
    ];

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
    }

   /**
    * getVenda
    *
    * pega a instancia do cidade
    *
    */
    public function getVendaTablet() {
        return new $this->entity();
    }

   /**
    * grid
    *
    * funcao usada para gerar o grid
    *
    */
    public function grid() {
        $this->db->from( $this->table.' vt' )
        ->select( ' CodVendaTablet as Código,
                    f.NeoCode as NeoCode, 
                    f.CPF, 
                    t.Nome as Tablet, 
                    vt.Quantidade, 
                    t.Pontos as Pontos Unitario,
                    vt.Pontos as Pontos Totais,
                    vt.Valor as Valor, 
                    vt.Data as Data, 
                    l.Nome as Loja, 
                    CodVendaTablet as Ações' )
        ->join( 'Funcionarios f', 'f.CodFuncionario = vt.CodFuncionario' )
        ->join( 'Tablets t', 't.CodTablet = vt.CodTablet' )
        ->join( 'Lojas l', 'l.CodLoja = vt.CodLoja' );
        return $this;
    }
    
   /**
    * grid
    *
    * funcao usada para gerar o grid
    *
    */
    public function exportar() {
        $this->db->from( $this->table.' vt' )
        ->select( 'CodVendaTablet as Código, f.CodFuncionario as Vendedor, f.NeoCode as NeoCode, f.CPF, t.Nome as Tablet, vt.Quantidade, vt.Pontos as Pontos,
         vt.Data as Data, l.Nome as Loja' )
        ->join( 'Funcionarios f', 'f.CodFuncionario = vt.CodFuncionario' )
        ->join( 'Tablets t', 't.CodTablet = vt.CodTablet' )
        ->join( 'Lojas l', 'l.CodLoja = vt.CodLoja' );
        return $this;
    }

}

/* end of file */
