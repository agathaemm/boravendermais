<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cluster extends MY_Model {

    // id do cluster
    public $CodCluster;

    // nome
    public $nome;

    // referencia
    public $ref;

    // entidade
    public $entity = 'Cluster';
    
    // tabela
    public $table = 'Clusters';

    // chave primaria
    public $primaryKey = 'CodCluster';

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
    }
    
    // seta o codigo
    public function setCod( $cod ) {
        $this->CodCluster = $cod;
    }

    // nome
    public function setNome( $nome ) {
        $this->nome = $nome;
    }

    // seta a referencia
    public function setRef( $ref ) {
        $this->ref = $ref;
        return $this;
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
            WHERE Lojas.CodCluster = $this->CodCluster
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
            WHERE Lojas.CodCluster = $this->CodCluster
            ORDER BY Cociente DESC ) as Rankeado ) as Posicao
        WHERE CodLoja = $loja" );

        // volta o resultado
        return ( $query->num_rows() > 0 ) ? $query->result_array()[0] : false;
    }
}

/* end of file */
