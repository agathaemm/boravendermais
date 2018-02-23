<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mascara_cnpj
 *
 * formata o cnpj
 *
 */
if ( ! function_exists('mascara_cnpj') ) {
    function mascara_cnpj( $valor ) {
        if ( strlen( $valor ) < 5 ) return $valor;
        $cnpj = substr( $valor, 0, 2 ).'.'.substr( $valor, 2, 3 ).'.'
                        .substr( $valor, 5, 3 ).'/'.substr( $valor, 8, 4 ).'-'.substr( $valor, 12, 2 );
        return $cnpj;
    }
}

/**
 * mascara_cep
 *
 * formata o cep
 *
 */
if ( ! function_exists( 'mascara_cep' ) ) {
    function mascara_cep( $valor ) {
        if ( strlen( $valor ) < 5 ) return $valor;
        $cep = substr( $valor, 0, 5 ).'-'.substr( $valor, 5, 3 );
        return $cep;       
    }
}

/**
 * mascara_cpf
 *
 * formata o cpf
 *
 */
if ( ! function_exists( 'mascara_cpf' ) ) {
    function mascara_cpf( $val ) {
        if ( strlen( $val ) < 5 ) return $val;
        return substr( $val, 0, 3 ).'.'.substr( $val, 3, 3 ).'.'.substr( $val, 6, 3).'-'.substr( $val, 9, 2 );
    }
}

/**
 * Remove uma máscara
 *
 */
if ( ! function_exists( 'remove_mask' ) ) {
    function remove_mask( $val, $chars = false ) {
        $chars = $chars ? $chars : [
            '(', ')', '-', '_', '.', '/', ' '
        ];
        return str_replace( $chars, '', $val );
    }
}

/**
 * mascara_rg
 *
 * formata o rg
 *
 */
if ( ! function_exists( 'mascara_rg' ) ) {
    function mascara_rg( $val ) {
        return substr( $val, 0, 3 ).'.'.substr( $val, 3, 3 ).'.'.substr( $val, 6, 3).'-'.substr( $val, 9, 2 );
    }
}


/**
 * mascara_telefone
 *
 * formata o telefone
 *
 */
if ( ! function_exists( 'mascara_telefone' ) ) {
    function mascara_telefone( $val ) {

        // seta o dd
        $dd = '('.$val[0].$val[1].')';

        // seta a primeira parte
        $prefix = ( strlen( $val ) == 8 ) ? substr( $val, 2, 4 ) : substr( $val, 2, 5 );

        // seta a segunda parte
        $pos = ( strlen( $val ) == 8 ) ? substr( $val, 5, 4 ) : substr( $val, 6, 4 );

        // volta os dados formatados
        return $dd.' '.$prefix.'-'.$pos;
    }
}

/**
 * in_cell
 *
 * verifica se existe
 *
 */
if ( ! function_exists( 'in_cell' ) ) {
    function in_cell( $val ) {
        $val = str_replace( [ ';', '-', ' ', "'", '"', ',' ], '', $val );

        // verifica se existe
        if ( !$val ) return false;

        // verificia o tamanho
        if ( strlen( $val ) == 0 ) return false;

        // verifica o conteudo
        if ( $val == '0' ) return false;

        // verifica o conteudo
        if ( trim( $val ) == '-' ) return false;

        // verifica o conteudo
        if ( trim( $val ) == ', -"' ) return false;

        // Verifica o trim
        if ( strlen( trim( $val ) ) == 0 ) return false;

        // volta true por padrao
        return true;
    }
}

/**
 * debug
 *
 * faz o debug do código
 *
 */
if ( ! function_exists( 'debug' ) ) {
    function debug( $var, $blocking = true ) {
        
        // imprime a pré visualizacao
        echo '<pre>';
    
        // verifica se é um dump bloqueante
        if ( $blocking )
            die( var_dump( $var ) );
        else
            var_dump( $var );
        
        // volta false
        return false;
    }
}

/**
 * flash
 * 
 * Seta uma mensagem temporária
 * 
 */
if ( ! function_exists( 'flash' ) ) {
    function flash( string $key, $value = null ) {

        // Pega a instancia do codeigniter
        $ci =& get_instance();

        // Verifica se existe um valor
        if ( $value === null ) {
            return $ci->session->flashdata( $key );
        } else {
            $ci->session->set_flashdata( $key, $value );
        }
    }
}
