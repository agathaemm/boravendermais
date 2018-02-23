<?php

require 'application/models/Tablet.php';

class TabletsFinder extends MY_Model {

    // entidade
    public $entity = 'Tablet';

    // tabela
    public $table = 'Tablets';

    // chave primaria
    public $primaryKey = 'CodTablet';

    // labels
    public $labels = [
        'Pontos'        => 'Pontos',
        'BasicCode'     => 'Basic Code'
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
    * getEstado
    *
    * pega a instancia do estado
    *
    */
    public function getTablet() {
        return new $this->entity();
    }

   /**
    * grid
    *
    * funcao usada para gerar o grid
    *
    */
    public function grid() {
        $this->db->from( $this->table .' t' )
        ->select( 't.BasicCode, t.Nome as Nome, t.Foto, t.Pontos, CodTablet as Ações' );
        return $this;
    }
    
    /**
    * exportar
    *
    * funcao usada para organizar os dados para exportacao
    *
    */
    public function exportar() {
        $this->db->from( $this->table .' t' )
        ->select( 't.CodTablet as Codigo, t.BasicCode, t.Nome as Nome, t.Pontos, t.Descricao, t.Foto, t.Video' );
        return $this;
    }   
    
    public function basicCode( $basiccode ) {
        $this->where( " BasicCode = '$basiccode' " );
        return $this;
    }

    /**
    * filtro
    *
    * volta o array para formatar os filtros
    *
    */
    public function filtro() {

        // prepara os dados
        $this->db->from( $this->table )
        ->select( 'CodTablet as Valor, Nome as Label' );

        // faz a busca
        $busca = $this->db->get();

        // verifica se existe resultados
        if ( $busca->num_rows() > 0 ) {

            // seta o array de retorna
            $ret = [];

            // percorre todos os dados
            foreach( $busca->result_array() as $item ) {
                $ret[$item['Valor']] = $item['Label'];
            }

            // retorna os dados
            return $ret;

        } else return [];
    }

    /**
     * Busca um talbet pelo nome
     *
     * @param [type] $nome
     * @return void
     */
    public function nome( $nome ) {

        $this->where( " Nome LIKE '$nome' " );
        return $this;
    }
}

/* end of file */
