<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rede extends MY_Model {

    /**
     * ID da Rede
     *
     * @var [type]
     */
    public $CodRede;

    /**
     * Cluster
     *
     * @var [type]
     */
    public $cluster;

    /**
     * Nome da rede
     *
     * @var [type]
     */
    public $nome;

    /**
     * Código de referência
     *
     * @var [type]
     */
    public $ref;

    /**
     * Entidade
     *
     * @var string
     */
    public $entity = 'Rede';
    
    /**
     * Nome da tabela no banco de dados
     *
     * @var string
     */
    public $table = 'Redes';

    /**
     * Nome da chave primária
     *
     * @var string
     */
    public $primaryKey = 'CodRede';

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
     * Seta o código
     *
     * @param [type] $cod
     * @return void
     */
    public function setCod( $cod ) {
        $this->CodRede = $cod;
        return $this;
    }

    /**
     * Seta o cluster
     *
     * @param [type] $funcionario
     * @return void
     */
    public function setCluster( $cluster ) {
        $this->cluster = $cluster;
        return $this;
    }

    /**
     * Seta o nome da rede
     *
     * @param [type] $data
     * @return void
     */
    public function setNome( $nome ) {
        $this->nome = $nome;
        return $this;
    }

    /**
     * Seta a referência
     *
     * @param [type] $ref
     * @return void
     */
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
            WHERE Lojas.CodRede = $this->CodRede
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
            WHERE Lojas.CodRede = $this->CodRede
            ORDER BY Cociente DESC ) as Rankeado ) as Posicao
        WHERE CodLoja = $loja" );

        // volta o resultado
        return ( $query->num_rows() > 0 ) ? $query->result_array()[0] : false;
    }
}

// End of file
