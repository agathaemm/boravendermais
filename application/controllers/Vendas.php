<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vendas extends MY_Controller {

    /**
     * Indica se o controller é público ou não
     *
     * @var boolean
     */
	protected $public = false;

   /**
    * __construct
    *
    * metodo construtor
    *
    */
    public function __construct() {
        parent::__construct();
        
        // carrega o finder
        $this->load->finder( [ 'FuncionariosFinder', 'LogsFinder', 'CategoriasFinder', 'ProdutosFinder', 'VendasFinder', 'LojasFinder' ] );
        
        // chama o modulo
        $this->view->module( 'navbar' )->module( 'aside' )->module( 'jquery-mask' );
    }

   /**
    * _formularioEstados
    *
    * valida o formulario de estados
    *
    */
    private function _formularioVenda() {

        // seta as regras
        $rules = [
            [
                'field' => 'cpf',
                'label' => 'CPF',
                'rules' => 'required|min_length[10]|trim'
            ], [
                'field' => 'quantidade',
                'label' => 'Quantidade',
                'rules' => 'required'
            ]
        ];


        // valida o formulário
        $this->form_validation->set_rules( $rules );
        return $this->form_validation->run();
    }

   /**
    * index
    *
    * mostra o grid de contadores
    *
    */
	public function index() {

        // carrega os categorias
        $lojas = $this->LojasFinder->filtro();

        // faz a paginacao
		$this->VendasFinder->clean()->grid()

        // seta os filtros
        ->order()
        ->addFilter( 'CodLoja', 'select', $lojas, 'v' )
        ->addFilter( 'CodFuncionario', 'text', false, 'f' )
        ->addFilter( 'NeoCode', 'text', false, 'f' )
		->filter()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'vendas/excluir/'.$row[$key] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';            
		})
        ->onApply( 'Data', function( $row, $key ) {
            echo '<small>'.date( 'd/m/Y', strtotime( $row[$key] ) ).'</small>';
        })

        // formata o Cnpj para exibicao
        ->onApply( 'Funcionario', function( $row, $key ) {
			echo mascara_cpf( $row[$key] );        
		})

		// renderiza o grid
		->render( site_url( 'vendas/index' ) );

        $export_url = site_url( 'vendas/exportar_planilha?' );

        foreach ($_GET as $key => $value) {
            if ( $key != 'page' ) {
                $export_url .= $key .'=' .$value .'&';
            }
        }

        $export_url = trim( $export_url, '&' );

        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'vendas/adicionar' ) )
        ->set( 'import_url', site_url( 'vendas/importar_planilha' ) )
        ->set( 'example_url', site_url( 'exemplos/vendas' ) )         
        ->set( 'export_url', $export_url );

		// seta o titulo da pagina
		$this->view->setTitle( 'Vendas - listagem' )->render( 'grid' );
    }

    /**
     * Exporta a planilha de vendas
     *
     * @return void
     */
    public function exportar_planilha() {

        // carrega os categorias
        $lojas = $this->LojasFinder->filtro();

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=LojasExportação".date( 'H:i d-m-Y', time() ).".xls" );

        // faz a paginacao
		$this->VendasFinder->clean()->exportar()
        ->addFilter( 'CodLoja', 'select', $lojas, 'v' )
        ->addFilter( 'CodFuncionario', 'text', false, 'f' )
        ->addFilter( 'NeoCode', 'text', false, 'f' )
		->filter()
        ->paginate( 1, 0, false, false )
        

        ->onApply( '*', function( $row, $key ) {
            echo strtoupper( mb_convert_encoding( $row[$key], 'UTF-16LE', 'UTF-8' ) );
        })
        
		// renderiza o grid
		->render( site_url( 'vendas/index' ) );

		// seta o titulo da pagina
		$this->view->component( 'table' );
    }

   /**
    * Adiciona uma nova equipe
    *
    */
    public function adicionar() {

        // carrega o jquery mask
        $this->view->module( 'jquery-mask' );

        // carrega os lojas
        $categorias = $this->CategoriasFinder->get();
        $this->view->set( 'categorias', $categorias );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Adicionar venda' )->render( 'forms/venda' );
    }

   /**
    * Exclui um item
    *
    */
    public function excluir( $key ) {

        // carrega a venda
        $venda = $this->VendasFinder->key( $key )->get( true );
        if ( !$venda ) {
            flash( 'error', 'Erro ao excluir a venda' );
            
            return;
        }

        // Carrega o funcionario
        $funcionario = $this->FuncionariosFinder->key( $venda->funcionario )->get( true );
        $funcionario->removePontos( $venda->pontos );

        // Carrega a loja
        $loja = $this->LojasFinder->key( $funcionario->loja )->get( true );
        $loja->setPontosAtuais( $loja->pontosatuais - $venda->valor, true );
        $loja->save();

        // Faz a exclusao do item
        if ( !$venda->delete() ) {
            flash( 'error', 'Erro ao remover o item!' );
        } else {
            flash( 'success', 'Item removido com sucesso!' );
        }
        redirect( site_url( 'vendas/index' ) );
    }

   /**
    * salva os dados
    *
    */
    public function salvar() {

        //carrega o produto da venda
        $produto = $this->ProdutosFinder->key( $this->input->post( 'produto' ) )->get( true );
        
        // carrega as categorias
        $categorias = $this->CategoriasFinder->get();
        $this->view->set( 'categorias', $categorias );        
        
        // carrega produtos da categoria selecionada
        $produtos = $this->ProdutosFinder->clean()->porCategoria( $this->input->post( 'categoria' ) )->get();
        $this->view->set( 'produtos', $produtos );

        $search = array('.','/','-');
        $cpf = str_replace ( $search , '' , $this->input->post( 'cpf') );

        // pega o funcionario da venda
        $funcionario = $this->FuncionariosFinder->cpf( $cpf )->get(true);

        $data = date( 'Y-m-d', strtotime( $this->input->post( 'data' ) ) );
        
        // instancia um novo objeto classificacao
        $venda = $this->VendasFinder->getVenda();  
        
        $venda->cpf = $cpf;

        $venda->categoria = $this->input->post( 'categoria' );
      
        $venda->setData( $data ); 
        $venda->setQuantidade( $this->input->post( 'quantidade' ) );  
        $venda->setCod( $this->input->post( 'cod' ) );

        // verifica se existe produto
        if( !$produto ) {

            $this->view->set( 'errors', $this->view->item( 'errors' ) ? 
                            $this->view->item( 'errors' ).'Selecione um produto!<br>' : 'Selecione um produto!<br>' );
            
        } else {

            $pontos = $produto->pontos * $this->input->post( 'quantidade' );
            $venda->setProduto( $this->input->post( 'produto' ) );
            $venda->setPontos( $pontos );  

        }
        
        // retorna erro caso o funcionario não exista no sistema
        if( !$funcionario ) {

            $this->view->set( 'errors', $this->view->item( 'errors' ) ? 
                            $this->view->item( 'errors' ).'Funcionário inexistente no sistema!<br>' : $view->item( 'errors' ).'Funcionário inexistente no sistema!<br>' );
        }  else {            

            $funcionario->addPontos( $pontos );
            
            $venda->setLoja( $funcionario->loja );
            $venda->setFuncionario( $funcionario->CodFuncionario );
        }


        // verifica se o formulario é valido
        if ( !$this->_formularioVenda() || $this->view->item( 'errors' ) ) {

            // seta os erros de validacao            
            $this->view->set( 'venda', $venda );
            $this->view->set( 'errors', $this->view->item( 'errors' ) ? 
                            $this->view->item( 'errors' ).validation_errors() : validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Samsung - Adicionar venda' )->render( 'forms/venda' );
            return;
        }

        // verifica se o dado foi salvo
        if ( $venda->save() ) {
            redirect( site_url( 'vendas/index' ) );
        }
    }

    /**
    * verificaEntidade
    *
    * verifica se um entidade existe no banco
    *
    */
    public function verificaEntidade( $finder, $method, $dado, $nome, $planilha, $linha, $attr, $status ) {

        // carrega o finder de logs
        $this->load->finder( 'LogsFinder' );

        // verifica se nao esta vazio
        if ( in_cell( $dado ) ) {

            // carrega o finder
            $this->load->finder( $finder );

            // pega a entidade
            if ( $entidade = $this->$finder->clean()->$method( $dado )->get( true ) ) {
                return $entidade->$attr;
            } else {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( $planilha )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'O campo '.$nome.' com valor '.$dado.' nao esta gravado no banco - linha '.$linha )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( $status )
                ->save();

                // retorna falso
                return null;
            }
        } else {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( $planilha )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Nenhum '.$nome.' encontrado - linha '.$linha )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( $status )            
            ->save();

            // retorna falso
            return null;
        }
    }

   /**
    * importar_linha
    *
    * importa a linha
    *
    */
    public function importar_linha( $linha, $num ) {

        // percorre todos os campos
        foreach( $linha as $chave => $coluna ) {
            $linha[$chave] = in_cell( $linha[$chave] ) ? $linha[$chave] : null;
        }

        // pega as entidades relacionaveis
        // Loja
        $linha['CodLoja'] = $this->verificaEntidade( 'LojasFinder', 'nome', $linha['NOMELOJA'], 'Lojas', 'Vendas', $num, 'CodLoja', 'I' ); 
        
        // Funcionario
        $linha['CodFuncionario'] = $this->verificaEntidade( 'FuncionariosFinder', 'cpf', $linha['CPFFUNCIONARIO'], 'Lojas', 'Vendas', $num, 'CodFuncionario', 'I' );

        // Produto
        $linha['CodProduto'] = $this->verificaEntidade( 'ProdutosFinder', 'basicCode', $linha['BASICCODE'], 'Produtos', 'Vendas', $num, 'CodProduto', 'I' );


        // verifica se existe um nome
        if ( !in_cell( $linha['CodProduto'] )  
        || !in_cell( $linha['QUANTIDADE'] )
        || !in_cell( $linha['CodLoja'] )
        || !in_cell( $linha['CodFuncionario'] )
        || !in_cell( $linha['DATA'] ) ) {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( 'Vendas' )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Não foi possivel inserir a Vendasenda pois os campos obrigatórios não foram informados, ou não estão corretos - linha '.$num )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( 'B' )            
            ->save();

        } else {

            // carrega o produto da venda
            $produto = $this->ProdutosFinder->clean()->key( $linha['CodProduto'] )->get( true );
            $pontos = $produto->pontos * $linha['QUANTIDADE'];

            // formata a data
            
            $data = substr( $linha['DATA'], 6, 4) .'-' .substr( $linha['DATA'], 3, 2) .'-' .substr( $linha['DATA'], 0, 2);        

            // verifica se carregou
            $venda = $this->VendasFinder->clean()->getVenda();

            // preenche os dados
            $venda->setFuncionario( $linha['CodFuncionario'] );
            $venda->setQuantidade( $linha['QUANTIDADE'] );
            $venda->setProduto( $linha['CodProduto'] );            
            $venda->setPontos( $pontos );
            $venda->setData( $data );
            $venda->setLoja( $linha['CodLoja'] );

            // tenta salvar a venda
            if ( $venda->save() ) {

                // carrega o finder da loja
                $this->load->finder( 'LojasFinder' );
                $loja = $this->LojasFinder->clean()->key( $linha['CodLoja'] )->get( true );

                $loja->setPontosAtuais( $loja->pontosatuais += $l['tvalor'] );
                $loja->save();

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Vendas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Venda criada com sucesso - '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'S' )            
                ->save();

            } else {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Vendas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Não foi possivel inserir a Venda - linha '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'B' )            
                ->save();
            }
        }
    }

   /**
    * importar_planilha
    *
    * importa os dados de uma planilha
    *
    */
    public function importar_planilha_antiga() {

        // importa a planilha
        $this->load->library( 'Planilhas' );

        // faz o upload da planilha
        $planilha = $this->planilhas->upload();

        // tenta fazer o upload
        if ( !$planilha ) {

            // seta os erros
            $this->view->set( 'errors', $this->planilhas->errors );
        } else {
            $planilha->apply( function( $linha, $num ) {
                $this->importar_linha_nova( $linha, $num );
            });
            $planilha->excluir();
        }

        // carrega a view
        $this->index();
    }

   /**
    * importar_linha_nova
    *
    * importa a linha
    *
    */
    public function importar_linha_nova( $linha, $num ) {

        $l = $linha;

        // percorre todos os campos
        
        // pega as entidades relacionaveis
        $neoCode = str_replace( [ '(', ')', ' ', '-', '.', '_' ], '', $l['CODNEOTASS']);

        // Funcionario
        $l['CodFuncionario'] = $this->verificaEntidade( 'FuncionariosFinder', 'neoCode', $neoCode, 'Funcionarios', 'Vendas', $num, 'CodFuncionario', 'I' );

        // busca o funcionario pelo neoCode
        $func = $this->FuncionariosFinder->clean()->neoCode( $neoCode )->get( true );
        
        $l['CodLoja'] = false;
        if( $func ){

            // Loja
            $l['CodLoja'] = $this->verificaEntidade( 'LojasFinder', 'key', $func->loja, 'Lojas', 'Vendas', $num, 'CodLoja', 'I' );
        }
        
        // pega os 7 primeiros digitos do codigo do produto
        $refProduto = substr( $l['Referência'], 0, 7 );
        
        // Produto
        $l['CodProduto'] = $this->verificaEntidade( 'ProdutosFinder', 'basicCode', $refProduto, 'Produtos', 'Vendas', $num, 'CodProduto', 'I' );

        // carrega o produto da venda
        $produto = $this->ProdutosFinder->clean()->key( $l['CodProduto'] )->get( true );

        // ve a quantidade pelos pontos gerados pela venda
        $qtd = $l['tponto'] / $produto->pontos;

        // verifica se existe um nome
        if ( !in_cell( $l['CodProduto'] )  
        || !in_cell( $l['tponto'] )
        || !in_cell( $l['CodLoja'] )
        || !in_cell( $l['CodFuncionario'] )
        || !in_cell( $l['DataDocumento'] ) 
        || ( $qtd != $l['Quantidade'] && $produto->pontos != $l['ponto'] ) ) {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( 'Vendas' )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Não foi possivel inserir a Vendasenda pois os campos obrigatórios não foram informados, ou não estão corretos - linha '.$num )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( 'B' )            
            ->save();

        } else {

            // formata a data
            if( strlen( $l['DataDocumento'] ) == 9 ) $data = substr( $l['DataDocumento'], 5, 4) .'-' .substr( $l['DataDocumento'], 3, 1) .'-' .substr( $l['DataDocumento'], 0, 2);
            if( strlen( $l['DataDocumento'] ) == 8 ) $data = substr( $l['DataDocumento'], 4, 4) .'-' .substr( $l['DataDocumento'], 2, 1) .'-' .substr( $l['DataDocumento'], 0, 1);
            
            // carrega o finder da loja
            $this->load->finder( 'LojasFinder' );
            $loja = $this->LojasFinder->clean()->key( $l['CodLoja'] )->get( true );

            $loja->setPontosAtuais( $loja->pontosatuais += $l['tvalor'] );
            $loja->save();

            // adiciona os pontos
            $func->addPontos( $l['tponto'] );

            // salva
            $func->save();

            // verifica se carregou
            $venda = $this->VendasFinder->clean()->getVenda();

            // preenche os dados
            $venda->setFuncionario( $l['CodFuncionario'] );
            $venda->setQuantidade( $l['Quantidade'] );
            $venda->setProduto( $l['CodProduto'] );            
            $venda->setPontos( $l['tponto'] );
            $venda->setData( $data );
            $venda->setLoja( $l['CodLoja'] );

            // tenta salvar a venda
            if ( $venda->save() ) {

                // loja
                $this->load->finder( 'LojasFinder' );
                $loja = $this->LojasFinder->clean()->key($l['CodLoja'] )->get( true );
                $loja->setPontosAtuais( $l['tvalor'] );
                $loja->save();

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Vendas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Venda criada com sucesso - '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'S' )            
                ->save();

            } else {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Vendas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Não foi possivel inserir a Venda - linha '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'B' )            
                ->save();
            }
        }
    }
    
   /**
    * importar_linha_nova
    *
    * importa a linha
    *
    */
    public function importar_linha_pontos( $linha, $num ) {
        
        $l = $linha;

        // percorre todos os campos
        //foreach( $linha as $chave => $coluna ) {
          //  $a = utf8_encode($chave);
          //  $t = utf8_encode( $linha[$chave] );
          //  $l[$a] = in_cell( $linha[$chave] ) ? $t : null;
        // }

        $loja = $this->LojasFinder->clean()->nome( trim( $l['PDV'] ) )->get( true );

        if( !$loja ) {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( 'Lojas' )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Não foi possivel inserir os pontos iniciais pois o PDV esta incorreto - linha '.$num )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( 'B' )            
            ->save();
            return;
        }
        
        // pega os 7 primeiros digitos do codigo do produto
        $refProduto = substr( $l['Referência'], 0, 7 );
        
        // Produto
        $l['CodProduto'] = $this->verificaEntidade( 'ProdutosFinder', 'basicCode', $refProduto, 'Produtos', 'Vendas', $num, 'CodProduto', 'I' );

        // carrega o produto da venda
        $produto = $this->ProdutosFinder->clean()->key( $l['CodProduto'] )->get( true );

        // ve a quantidade pelos pontos gerados pela venda
        $pontos = $l['Quantidade'] * $produto->pontos;

        // verifica se existe um nome
        if ( !in_cell( $l['CodProduto'] ) ) {

            // grava o log
            $this->LogsFinder->getLog()
            ->setEntidade( 'Lojas' )
            ->setPlanilha( $this->planilhas->filename )
            ->setMensagem( 'Não foi possivel inserir os pontos iniciais pois os campos obrigatórios não foram informados, ou não estão corretos - linha '.$num )
            ->setData( date( 'Y-m-d H:i:s', time() ) )
            ->setStatus( 'B' )            
            ->save();

        } else {

            // preenche os dados
            $loja->setPontosIniciais( $loja->pontosiniciais + $l['ValorTotal'] );

            // tenta salvar a venda
            if ( $loja->save() ) {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Lojas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Pontos iniciais alterado com sucesso - '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'S' )            
                ->save();

            } else {

                // grava o log
                $this->LogsFinder->getLog()
                ->setEntidade( 'Lojas' )
                ->setPlanilha( $this->planilhas->filename )
                ->setMensagem( 'Não foi possivel inserir os pontos iniciais - linha '.$num )
                ->setData( date( 'Y-m-d H:i:s', time() ) )
                ->setStatus( 'B' )            
                ->save();
            }
        }
    }

    /**
     * Faz a importação de uma linha da planilha
     *
     * @param [type] $linha
     * @param [type] $num
     * @return void
     */
    public function __importarLinha( $row, $num ) {
        $linha = lower_case_keys( $row );

        // Obtem o funcionário
        $func = $this->FuncionariosFinder->clean()->neoCode( $linha['neocode'] )->get( true );
        if ( !$func ) {
            $this->import->insertLine( $row, 'NENHUM FUNCIONARIO '.$linha['neocode'] );
            return;
        }

        // procura a loja
        $linha['CodLoja'] = $this->verificaEntidade( 'LojasFinder', 'key', $func->loja, 'Lojas', 'Vendas', $num, 'CodLoja', 'I' );
        if ( !in_cell( $linha['CodLoja'] ) ) {
            $this->import->insertLine( $row, 'NENHUMA LOJA ENCONTRADA PARA O FUNCIONARIO '.$linha['neocode'] );
            return;
        }

        // pega os 7 primeiros digitos do codigo do produto
        $refProduto = substr( $linha['basiccode'], 0, 7 );
        
        // Produto
        $linha['CodProduto'] = $this->verificaEntidade( 'ProdutosFinder', 'basicCode', $refProduto, 'Produtos', 'Vendas', $num, 'CodProduto', 'I' );
        if ( !in_cell( $linha['CodProduto'] ) ) {
            $this->import->insertLine( $row, 'NENHUM PRODUTO ENCONTRADO' );
            return;
        }
        
        // carrega o produto da venda
        $produto = $this->ProdutosFinder->clean()->key( $linha['CodProduto'] )->get( true );
        if ( !$produto ) {
            $this->import->insertLine( $row, 'NENHUM PRODUTO ENCONTRADO' );
            return;
        }
        
        // ve a quantidade pelos pontos gerados pela venda
        if ( !in_cell( $linha['totalpontos'] ) ) {
            $this->import->insertLine( $row, 'TOTAL DE PONTOS NAO ESPECIFICADO' );
            return;
        }
        $qtd = $linha['totalpontos'] / $produto->pontos;
        if ( $qtd != $linha['quantidade'] || $produto->pontos != $linha['ponto'] ) {
            $this->import->insertLine( $row, 'VALORES DE PONTOS NAO BATEM COM O SISTEMA' );
            return;
        }

        // ve a quantidade pelos pontos gerados pela venda
        if ( !in_cell( $linha['totalvalor'] ) ) {
            $this->import->insertLine( $row, 'NENHUM VALOR ESPECIFICADO' );
            return;
        }

        // verifica se a data esta batendo
        if ( !in_cell(  $linha['data']  ) ) {
            $this->import->insertLine( $row, 'DATA OBRIGATORIA' );
            return;
        }
        $data = $linha['data'];
        if( strlen( $linha['data'] ) == 9 ) $data = substr( $linha['data'], 5, 4) .'-' .substr( $linha['data'], 3, 1) .'-' .substr( $linha['data'], 0, 2);
        if( strlen( $linha['data'] ) == 8 ) $data = substr( $linha['data'], 4, 4) .'-' .substr( $linha['data'], 2, 1) .'-' .substr( $linha['data'], 0, 1);
        $data = date( 'Y-m-d H:i:s', strtotime( $data ) );

        // carrega o finder da loja
        $this->load->finder( 'LojasFinder' );
        $loja = $this->LojasFinder->clean()->key( $linha['CodLoja'] )->get( true );

        // Seta os pontos atuais da loja
        $loja->setPontosAtuais( $linha['totalvalor'] );
        $loja->save();

        // adiciona os pontos do funcionario
        $func->addPontos( $linha['totalpontos'] );
        $func->save();

        // Cria a venda
        $venda = $this->VendasFinder->clean()->getVenda();
        $venda->setFuncionario( $func->CodFuncionario );
        $venda->setQuantidade( $linha['quantidade'] );
        $venda->setProduto( $produto->CodProduto );            
        $venda->setPontos( $linha['totalpontos'] );
        $venda->setData( $data );
        $venda->setValor( $linha['totalvalor'] );
        $venda->setLoja( $linha['CodLoja'] );

        // Verifica se conseguiu salvar a venda
        if ( !$venda->save() ) {
            $this->import->insertLine( $row, 'ERRO AO SALVAR' );
        }
    }

    /**
     * Faz a importação da planilha
     *
     * @return void
     */
    public function importar_planilha() {

        // importa a planilha
        $this->load->library( 'Planilhas' );

        // faz o upload da planilha
        $planilha = $this->planilhas->upload();

        // tenta fazer o upload
        if ( !$planilha ) {

            // seta os erros
            $this->view->set( 'errors', $this->planilhas->errors );
        } else {

            // Verifica se o header é válido
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'vendas' ) ) {
                
                // seta os erros
                array_unshift( $missing, '<br><b>Os campos faltantes são:</b>' );
                array_unshift( $missing, 'Caso tenha duvidas de como importar, clique no botão <b>Visualizar exemplo de Importação</b> logo abaixo.' );
                array_unshift( $missing, 'A planilha que você tentou importar não possui todos os campos requeridos.' );
                $this->view->set( 'errors', $missing );
            } else {
                $this->import->cleanTable();
                $this->planilhas->apply( function( $a, $b ) {
                    $this->__importarLinha( $a , $b );
                });
                if ( $this->import->hasNoImportedLines() ) {
                    // seta os erros
                    $this->view->set( 'warnings', [
                        'A importação foi finalizada, porém algumas linhas não foram importadas.',
                        '<b>Para fazer o download de todas as linhas não importadas clique no link abaixo:</b>',
                        '<a href="'.site_url( 'exemplos/export_import_errors').'" target="blank">Clique aqui para baixar a planilha</a>'
                    ]);
                } else {
                    flash( 'success', 'Todas as linhas foram importadas com sucesso!' );
                }
            }

            $planilha->excluir();
        }

        // Abre a index
        $this->index();
    }

    // public function calcular_pontos_iniciais() {

    //     // faz a busca
    //     $query = $this->db->query( " select SUM( Pontos ) as total, CodLoja from Vendas GROUP BY CodLoja " );

    //     // percorre os dados
    //     foreach( $query->result_array() as $item ) {

    //         // carrega o finder de lojas
    //         $this->load->finder( [ 'LojasFinder' ] );

    //         // carrega a loja
    //         $loja = $this->LojasFinder->clean()->key( $item['CodLoja'] )->get( true );
    //         $loja->setPontosIniciais( $item['total'] );
    //         $loja->save();
    //     }
    // }
}

// End of file
