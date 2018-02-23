<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Planilhas
*
* classe de manipulacao de planilhas
*
*/
class Planilhas {

    // instancia do ci
    public $ci;

    // erros
    public $errors;

    // ultimo arquivo carregado
    public $filename;

    // pega o header
    public $header = [];

    // linhas do arquivo
    public $linhas;

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {

        // pega a instancia do codeigniter
        $this->ci =& get_instance();
    }

    /**
     * Obtem o header da planilha
     *
     * @return void
     */
    public function getHeader() {
        $this->apply();
        return $this->header;
    }

    /**
     * Obtem o delimitador de uma linha do csv
     *
     * @param [type] $line
     * @return void
     */
    public function getDelimiter( $line ) {

        // Seta os delimitadores possiveis
        $delimiters = [ ',', ';' ];
        $line = trim( $line );

        // Seta o retorno
        $retorno = $delimiters[0];
        
        // Percorre todos os delimitadores
        $bigger = 0;
        foreach( $delimiters as $delimiter ) {
            $toCheck = str_getcsv( $line, $delimiter );
            if ( is_array( $toCheck ) && count( $toCheck ) > $bigger ) {
                $bigger  = count( $toCheck );
                $retorno = $delimiter;
            }
        }

        // Volta o delimitador encontrado
        return $retorno;
    }   

    /**
     * Aplica uma funcao para cada linha da planilha
     *
     * @param [type] $callback
     * @return void
     */
    public function apply( $callback = false ) {

        // path url
        $path = 'uploads/'.$this->filename;

        // Obtem os controles do CSV
        $file = new SplFileObject( $path );

        // Percorre as linhas
        foreach ( $file as $line ) {
            $delimiter = $this->getDelimiter( $line );
            
            // Obtem o array do csv
            $content = str_getcsv( $line, $delimiter );
            $content = array_filter( $content, function( $value ) {
                return in_cell( $value );
            }); 
            if ( count( $content ) == 0 ) continue;
            
            // Verifica se já existe um header
            if ( count( $this->header ) == 0 ) {
                $this->header = $content;
                continue;
            }

            // Verifica a linha
            if ( $file->key() == 0 ) continue;

            // Verifica se existe um callback
            if ( !$callback ) break;

            // Percorre todos os itens do header
            $line_content = [];
            for ( $i = 0; $i < count( $this->header ); $i++ ) {
                $line_content[$this->header[$i]] = isset( $content[$i] ) ?  $content[$i] : null;
            }

            // aplica o callback
            if ( $callback && count( $line_content ) > 0 ) $callback( $line_content, $file->key() + 1 );
        }

        /*  inicializa a linha
        $row = 1;
        if ( ( $handle = fopen( $path, "r" ) ) !== FALSE ) {
            $break = false;
            while ( ( $data = fgetcsv($handle, 1000, $controls[0], $controls[1], $controls[2] ) ) !== FALSE && !$break ) {

                // verifica se é a primeira linha
                if ( $row == 1 ) {

                    // pega o cabecalho
                    $header = $data;
                    $this->header = $data;
                } else if ( $callback ) {

                    // pega a quatidade de itens na linha
                    $num = count($data);

                    // array de resposta
                    $res = [];

                    // percorre cada um deles
                    for ( $c=0; $c < $num; $c++ ) {
                        $key = str_replace( ' ', '', $header[$c] );
                        $res[$key] = $data[$c];
                    }

                    // aplica o callback
                    $callback( $res, $row );
                } else {
                    $break;
                }

                // soma a linha
                $row++;                
            }
            fclose($handle);
            return true;
        } */
    }

   /**
    * upload
    *
    * faz o upload da planilha
    *
    */
    public function upload() {

        // seta as configuracoes
        $config['file_name']     = md5( uniqid( rand() * time() ) );
        $config['upload_path']   = './uploads/';
        $config['allowed_types'] = 'csv';

        // carrega a lib de upload
        $this->ci->load->library( 'upload', $config );

        // tenta fazer o upload
        if ( !$this->ci->upload->do_upload( 'planilha' ) ) {

            // seta os erros
            $this->errors = [ 'error' => $this->ci->upload->display_errors() ];
            return false;
        } else {
            $data = $this->ci->upload->data();

            $this->filename = $data['file_name'];
            return $this;
        }
    }

   /**
    * excluir
    *
    * exclui a planilha
    *
    */
    public function excluir() {

        // path
        $path = './uploads/'.$this->filename;

        // verifica se o arquivo existe
        if ( file_exists( $path ) ) {
            return unlink( $path );
        } return false;
    }
}

/* end of file */
