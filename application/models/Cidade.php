<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cidade extends MY_Model {

    // id da cidade
    public $CodCidade;

    // estado
    public $estado;

    // nome
    public $nome;

    // entidade
    public $entity = 'Cidade';
    
    // tabela
    public $table = 'Cidades';

    // chave primaria
    public $primaryKey = 'CodCidade';

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
        $this->CodCidade = $cod;
    }

    // nome
    public function setNome( $nome ) {
        $this->nome = $nome;
    }

    // uf
    public function setEstado( $estado ) {
        $this->estado = $estado;
    }

    /**         
     * Obtem os primeiros colocados
     *
     */
    public function obterPrimeirosColocados() {

        $query = $this->db->query( "SELECT Rankeado.*, @i := @i+1 AS ranking
            FROM (SELECT @i:=0) AS foo,
            ( SELECT 	Lojas.CodLoja, 
                        Lojas.Nome, 
                        ( ( Lojas.PontosAtuais / Lojas.PontosIniciais ) * 100 ) as Cociente 
            FROM Lojas 
            WHERE Lojas.CodCidade = $this->CodCidade
            ORDER BY Cociente DESC ) as Rankeado
            LIMIT 10 " );

        // faz a busca
        return $query->result_array();
    }

    /**
     * Obtem a posicao da loja
     *
     */
    public function obterLojaPosicao( $loja ) {

        // prepara a query
        $query = $this->db->query( "SELECT * FROM ( SELECT Rankeado.*, @i := @i+1 AS ranking
            FROM (SELECT @i:=0) AS foo,
            ( SELECT 	Lojas.CodLoja, 
                        Lojas.Nome, 
                        ( ( Lojas.PontosAtuais / Lojas.PontosIniciais ) * 100 ) as Cociente 
            FROM Lojas 
            WHERE Lojas.CodCidade = $this->CodCidade
            ORDER BY Cociente DESC ) as Rankeado ) as Posicao
        WHERE CodLoja = $loja" );

        // volta o resultado
        return ( $query->num_rows() > 0 ) ? $query->result_array()[0] : false;
    }
}

/* end of file */
