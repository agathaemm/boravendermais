<?php

require 'application/models/Categoria.php';

class CategoriasFinder extends MY_Model {

    // entidade
    public $entity = 'Categoria';

    // tabela
    public $table = 'Categorias';

    // chave primaria
    public $primaryKey = 'CodCategoria';

    // labels
    public $labels = [
        'Nome'  => 'Nome',
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
    public function getCategoria() {
        return new $this->entity();
    }

    /**
     * Obtem uma categoria pelo código de referencia
     *
     * @param [type] $cod
     * @return void
     */
    public function byRefCode( $cod ) {
        $this->where( " Ref = '$cod' " );
        return $this;
    }

   /**
    * grid
    *
    * funcao usada para gerar o grid
    *
    */
    public function grid() {
        $this->db->from( $this->table )
        ->select( 'CodCategoria as Código, Ref as Referência, Nome, Foto, CodCategoria as Ações' );
        return $this;
    }   

    /**
     * Busca uma categoria pelo nome
     *
     * @param [type] $nome
     * @return void
     */
    public function nome( $nome ) {

        $this->where( " Nome LIKE '$nome' " );
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
        ->select( 'CodCategoria as Valor, Nome as Label' );

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
}

/* end of file */
