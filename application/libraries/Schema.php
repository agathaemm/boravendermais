<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Classe para pegas os esquemas de importação
 *
 */
class Schema {

    /**
     * Caminho dos imports
     *
     * @var string
     */
    public $path = 'application/import_schemas/';

    /**
     * Obtem o esquema de um arquivo
     *
     * @param [type] $file
     * @return void
     */
    public function get( $file ) {

        // Pega o conteudo de um arquivo
        $content = file_get_contents( $this->path.$file.'.json' );

        // Faz o parse do JSON
        $json = json_decode( $content, true );

        // Retorna o json encontrado
        return $json;
    }

    /**
     * Indica se um conjunto de dados é um esquema válido
     * de acordo com o source informado
     *
     * @param [type] $data
     * @param [type] $source
     * @return void
     */
    public function invalid( $data, $source ) {
        
        // Obtem o schema
        $source = $this->get( $source );
        
        // Transforma os dados para minusculo
        $dataLower = array_map( function( $value ) {
            return strtolower( $value );
        }, $data );
        $sourceLower = array_map( function( $value ) {
            return strtolower( $value );
        }, array_keys( $source ) );

        // Verifica se todos os itens de source estão presentes no header
        $diff = array_diff( $sourceLower, $dataLower );
        foreach( $diff as $key => $value ) $diff[$key] = '<b>- '.array_keys($source)[$key].'</b>';

        // Volta o resultado encontrado
        return ( count( $diff ) > 0 ) ? $diff : false;
    }
}

// End of file
