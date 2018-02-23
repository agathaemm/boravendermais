<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Loja extends MY_Model {

    // id do cluster
    public $CodLoja;

    // cluster
    public $cluster;

    // cnpj
    public $cnpj;

    // razao
    public $razao;

    // nome
    public $nome;

    // endereco
    public $endereco;

    // numero
    public $numero;

    // complemento
    public $complemento;

    // bairro
    public $bairro;

    // cidade
    public $cidade;

    // estado
    public $estado;

    // rede
    public $rede;
    
    // pontosiniciais
    public $pontosiniciais;

    // pontos atuais
    public $pontosatuais;

    // entidade
    public $entity = 'Loja';
    
    // tabela
    public $table = 'Lojas';

    // chave primaria
    public $primaryKey = 'CodLoja';

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
        $this->CodLoja = $cod;
    }

    // cluster
    public function setCluster( $cluster ) {
        $this->cluster = $cluster;
    }

    // cnpj
    public function setCNPJ( $cnpj ) {
        $this->cnpj = $cnpj;
    }

    // rede
    public function setRede( $rede ) {
        $this->rede = $rede;
        return $this;
    } 

    // razao
    public function setRazao( $razao ) {
        $this->razao = $razao;
    }    

    // nome
    public function setNome( $nome ) {
        $this->nome = $nome;
    }

    // endereco
    public function setEndereco( $endereco ) {
        $this->endereco = $endereco;
    }  

    // numero
    public function setNumero( $numero ) {
        $this->numero = $numero;
    }

    // complemento
    public function setComplemento( $complemento ) {
        $this->complemento = $complemento;
    }

    // bairro
    public function setBairro( $bairro ) {
        $this->bairro = $bairro;
    }

    // cidade
    public function setCidade( $cidade ) {
        $this->cidade = $cidade;
    }

    // estado
    public function setEstado( $estado ) {
        $this->estado = $estado;
    }

    // pontosiniciais
    public function setPontosIniciais( $pontosiniciais ) {
        $this->pontosiniciais = $pontosiniciais;
    }

    // pontosfinais
    public function setPontosAtuais( $pontosatuais, $force = false ) {
        if ( !$force ) {
            $this->pontosatuais += $pontosatuais;
        } else {
            $this->pontosatuais = $pontosatuais;
        }
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
            ORDER BY Cociente DESC ) as Rankeado ) as Posicao
        WHERE CodLoja = $loja" );

        // volta o resultado
        return ( $query->num_rows() > 0 ) ? $query->result_array()[0] : false;
    }
}

/* end of file */
