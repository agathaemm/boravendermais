<?php

require 'application/models/Rede.php';

/**
 * Finder de redes
 *
 */
class RedesFinder extends MY_Model {

    /**
     * Entidade do finder
     *
     * @var string
     */
    public $entity = 'Rede';

    /**
     * Nome da tabela
     *
     * @var string
     */
    public $table = 'Redes';

    /**
     * Chave primária
     *
     * @var string
     */
    public $primaryKey = 'CodRede';

    /**
     * Labels das redes
     *
     * @var array
     */
    public $labels = [];

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
     * Obtem uma instancia de Rede
     *
     */
    public function getRede() {
        return new $this->entity();
    }

    /**
     * Metódo usado para gerar o GRID
     *
     */
    public function grid() {
        $this->db->from( $this->table.' d' )
        ->select( 'CodRede as Código, d.Ref as Referência, d.Nome, d.CodRede as Ações' )
        ->join( 'Clusters c', 'c.CodCluster = d.CodCluster', 'left' );
        return $this;
    }

    /**
     * Obtem uma rede pelo código de referencia
     *
     * @param [type] $cod
     * @return void
     */
    public function byRefCode( $cod ) {
        $this->where( " Ref LIKE '$cod' " );
        return $this;
    }

    /**
     * Filtra redes por cluster
     *
     * @param [type] $cluster
     * @return void
     */
    public function porCluster( $cluster ) {
        $this->where( " CodCluster = $cluster " );
        return $this;
    }

    /**
     * Encontra uma rede pelo nome
     *
     * @param [type] $nome
     * @return void
     */
    public function nome( $nome ) {
        
        // seta o where
        $this->where( " Nome LIKE '$nome' " );
        return $this;
    }
}

// End of file
