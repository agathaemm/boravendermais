<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Lojas extends MY_Controller {

    // indica se o controller é publico
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
        $this->load->finder( [  'LojasFinder', 
                                'CidadesFinder', 
                                'EstadosFinder', 
                                'RedesFinder',
                                'ClustersFinder' ] );
        
        // chama o modulo
        $this->view->module( 'navbar' )->module( 'aside' )->module( 'jquery-mask' );
    }

   /**
    * _formularioEstados
    *
    * valida o formulario de estados
    *
    */
    private function _formularioLoja() {

        // seta as regras
        $rules = [
            [
                'field' => 'cluster',
                'label' => 'Cluster',
                'rules' => 'required'
            ], [
                'field' => 'cnpj',
                'label' => 'CNPJ',
                'rules' => ''
            ], [
                'field' => 'razao',
                'label' => 'Razao',
                'rules' => 'required|min_length[3]|trim'
            ], [
                'field' => 'nome',
                'label' => 'Nome',
                'rules' => 'required|min_length[3]|trim'
            ], [
                'field' => 'endereco',
                'label' => 'Endereco',
                'rules' => 'min_length[3]|trim'
            ], [
                'field' => 'numero',
                'label' => 'Numero',
                'rules' => 'min_length[1]|trim'
            ], [
                'field' => 'complemento',
                'label' => 'Complemento',
                'rules' => 'min_length[3]|trim'
            ], [
                'field' => 'bairro',
                'label' => 'Bairro',
                'rules' => 'min_length[3]|trim'
            ], [
                'field' => 'cidade',
                'label' => 'Cidade',
                'rules' => 'min_length[1]|trim'
            ], [
                'field' => 'estado',
                'label' => 'Estado',
                'rules' => 'min_length[1]|trim'
            ], [
                'field' => 'pontosiniciais',
                'label' => 'PontosIniciais',
                'rules' => 'required'
            ],
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
        $clusters = $this->ClustersFinder->filtro();

        // faz a paginacao
		$this->LojasFinder->clean()->grid()

		// seta os filtros
        ->addFilter( 'Razao', 'text' )
        ->addFilter( 'Nome', 'text', false, 'l' )
        ->addFilter( 'CodCluster', 'select', $clusters, 'l')
		->filter()
		->order()
		->paginate( 0, 20 )

		// seta as funcoes nas colunas
		->onApply( 'Ações', function( $row, $key ) {
			echo '<a href="'.site_url( 'lojas/alterar/'.$row[$key] ).'" class="margin btn btn-xs btn-info"><span class="glyphicon glyphicon-pencil"></span></a>';
			echo '<a href="'.site_url( 'lojas/excluir/'.$row[$key] ).'" class="margin btn btn-xs btn-danger"><span class="glyphicon glyphicon-trash"></span></a>';            
		})

        // formata o Cnpj para exibicao
        ->onApply( 'CNPJ', function( $row, $key ) {
			echo mascara_cnpj( $row[$key] );        
        })
        ->onApply( 'Crescimento', function( $row, $key ) {
            $row['PontosIniciais']  = $row['PontosIniciais']  == 0 ? 1 : $row['PontosIniciais'] ;
            echo ( number_format( $row[$key], 2 ) * 100 ).' %';
        })

		// renderiza o grid
		->render( site_url( 'lojas/index' ) );
		
        // seta a url para adiciona
        $this->view->set( 'add_url', site_url( 'lojas/adicionar' ) )
        ->set( 'import_url', site_url( 'lojas/importar_planilha' ) )
        ->set( 'example_url', site_url( 'exemplos/lojas' ) )   
        ->set( 'export_url', site_url( 'lojas/exportar_planilha' ) );

		// seta o titulo da pagina
		$this->view->setTitle( 'Lojas - listagem' )->render( 'grid' );
    }

    /**
     * Exporta a planilha de lojas
     *
     * @return void
     */
    public function exportar_planilha() {

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=LojasExportação".date( 'H:i d-m-Y', time() ).".xls" );

        // faz a paginacao
		$this->LojasFinder->clean()->exportar()
        ->paginate( 1, 0, false, false )

        ->onApply( '*', function( $row, $key ) {
            echo strtoupper( mb_convert_encoding( $row[$key], 'UTF-16LE', 'UTF-8' ) );
        })
        
		// renderiza o grid
		->render( site_url( 'lojas/index' ) );

		// seta o titulo da pagina
		$this->view->component( 'table' );
    }

   /**
    * adicionar
    *
    * mostra o formulario de adicao
    *
    */
    public function adicionar() {

        // carrega o jquery mask
        $this->view->module( 'jquery-mask' );

        // carrega os clusters
        $clusters = $this->ClustersFinder->get();
        $this->view->set( 'clusters', $clusters );

        // carrega os redes
        $redes = $this->RedesFinder->get();
        $this->view->set( 'redes', $redes );

        // carrega os estados
        $estados = $this->EstadosFinder->get();
        $this->view->set( 'estados', $estados );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Adicionar loja' )->render( 'forms/loja' );
    }

   /**
    * alterar
    *
    * mostra o formulario de edicao
    *
    */
    public function alterar( $key ) {

         // carrega o jquery mask
        $this->view->module( 'jquery-mask' );

        // carrega o classificacao
        $loja = $this->LojasFinder->key( $key )->get( true );

        // carrega os clusters
        $clusters = $this->ClustersFinder->get();
        $this->view->set( 'clusters', $clusters );

        // carrega os estados
        $estados = $this->EstadosFinder->get();
        $this->view->set( 'estados', $estados );
        
        // carrega as cidades
        $cidades = $this->CidadesFinder->clean()->porEstado( $loja->estado )->get();
        $this->view->set( 'cidades', $cidades );

        // carrega os redes
        $redes = $this->RedesFinder->get();
        $this->view->set( 'redes', $redes );

        // verifica se o mesmo existe
        if ( !$loja ) {
            redirect( 'lojas/index' );
            exit();
        }

        // salva na view
        $this->view->set( 'loja', $loja );

        // carrega a view de adicionar
        $this->view->setTitle( 'Samsung - Alterar loja' )->render( 'forms/loja' );
    }

   /**
    * excluir
    *
    * exclui um item
    *
    */
    public function excluir( $key ) {
        $loja = $this->LojasFinder->getLoja();
        $loja->setCod( $key );
        $loja->delete();
        $this->index();
    }

   /**
    * salvar
    *
    * salva os dados
    *
    */
    public function salvar() {

        // carrega os clusters
        $clusters = $this->ClustersFinder->get();
        $this->view->set( 'clusters', $clusters );

        // carrega as redes
        $redes = $this->RedesFinder->get();
        $this->view->set( 'redes', $redes );

        // carrega os cidades
        $cidades = $this->CidadesFinder->get();
        $this->view->set( 'cidades', $cidades );

        // carrega os estados
        $estados = $this->EstadosFinder->get();
        $this->view->set( 'estados', $estados );

        $search = array('.','/','-');
        $cnpj = str_replace ( $search , '' , $this->input->post( 'cnpj') );

        // instancia um novo objeto classificacao
        if( $this->input->post( 'cod' ) )
            $loja = $this->LojasFinder->clean()->key( $this->input->post( 'cod' ) )->get( true );
        else
            $loja = $this->LojasFinder->getLoja();

        // instancia um novo objeto classificacao
        $loja->setCluster( $this->input->post( 'cluster' ) );
        $loja->setRede( $this->input->post( 'rede' ) );
        $loja->setCnpj( $cnpj );
        $loja->setRazao( $this->input->post( 'razao' ) );
        $loja->setNome( $this->input->post( 'nome' ) );
        $loja->setEndereco( $this->input->post( 'endereco' ) );
        $loja->setNumero( $this->input->post( 'numero' ) );
        $loja->setBairro( $this->input->post( 'bairro' ) );
        $loja->setComplemento( $this->input->post( 'complemento' ) );
        $loja->setCidade( $this->input->post( 'cidade' ) );
        $loja->setEstado( $this->input->post( 'estado' ) );
        $loja->setPontosIniciais( $this->input->post( 'pontosiniciais' ) );
        $loja->pontosatuais = $this->input->post( 'pontosatuais' );

        // verifica se o formulario é valido
        if ( !$this->_formularioLoja() ) {

            // seta os erros de validacao            
            $this->view->set( 'loja', $loja );
            $this->view->set( 'errors', validation_errors() );
            
            // carrega a view de adicionar
            $this->view->setTitle( 'Samsung - Adicionar loja' )->render( 'forms/loja' );
            return;
        }

        // verifica se o dado foi salvo
        if ( $loja->save() ) {
            redirect( site_url( 'lojas/index' ) );
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
    public function importar_linha( $row, $num ) {
        $linha = lower_case_keys( $row );
    
        // percorre todos os campos
        foreach( $linha as $chave => $coluna ) {
            $linha[$chave] = in_cell( $linha[$chave] ) ? $linha[$chave] : null;
        }
        $linha['cnpj'] = remove_mask( $linha['cnpj'] );

        // pega as entidades relacionaveis
        $linha['CodEstado']  = $this->verificaEntidade( 'EstadosFinder',  'uf',   $linha['estado'],  ' Estados',  'Lojas', $num, 'CodEstado',   'I' );
        $linha['CodCidade']  = $this->verificaEntidade( 'CidadesFinder',  'nome', $linha['cidade'],  'Cidades',   'Lojas', $num, 'CodCidade',   'I' );
        $linha['CodRede']    = $this->verificaEntidade( 'RedesFinder',    'nome', $linha['rede'],    'Redes',     'Lojas', $num, 'CodRede',     'B' );
        $linha['CodCluster'] = $this->verificaEntidade( 'ClustersFinder', 'byRefCode', $linha['cluster'], 'Clusters',   'Lojas', $num, 'CodCluster', 'B' );
        if ( !in_cell( $linha['CodRede'] ) ) {
            $linha['CodRede'] = $this->verificaEntidade( 'RedesFinder', 'byRefCode', $linha['rede'], 'Redes', 'Lojas', $num, 'CodRede', 'B' );
        }
        if ( !in_cell( $linha['CodCluster'] ) ) {
            $this->import->insertLine( $row, 'NENHUM CLUSTER INFORMADO' );
            return;
        }

        // Verifica se a rede foi informada
        if ( !in_cell( $linha['CodRede'] ) ) {
            $this->import->insertLine( $row, 'NENHUMA REDE INFORMADA' );
            return;
        }

        // verifica se existe um nome
        if ( !in_cell( $linha['nome'] ) ) {

            // grava o log
            $this->import->insertLine( $row, 'NENHUM NOME INFORMADO' );

        } else {

            // tenta carregar a loja pelo nome
            $loja   = $this->LojasFinder->clean()->nome( $linha['nome'] )->get( true );
            $rede   = $this->RedesFinder->clean()->key( $linha['CodRede'] )->get( true );
            if ( in_cell( $linha['CodCidade'] ) && !in_cell( $linha['CodEstado'] ) ) {
                $cidade = $this->CidadesFinder->clean()->key( $linha['CodCidade'] )->get( true );
                $linha['CodEstado'] = $cidade->estado;
            }
            
            
            // verifica se carregou
            if ( !$loja ) $loja = $this->LojasFinder->getLoja();

            // preenche os dados
            $loja->setRazao( $linha['razao'] );
            $loja->setCNPJ( $linha['cnpj'] );
            $loja->setNome( $linha['nome'] );
            $loja->setCluster( $linha['CodCluster'] );
            $loja->setRede( $linha['CodRede'] );
            $loja->setCidade( $linha['CodCidade'] );
            $loja->setEstado( $linha['CodEstado'] );
            $loja->setBairro( $linha['bairro'] );
            $loja->setComplemento( $linha['complemento'] );
            $loja->setNumero( $linha['numero'] );
            $loja->setPontosIniciais( $linha['pontos'] );

            // tenta salvar a loja
            if ( !$loja->save() ) $this->import->insertLine( $row );
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
            if ( $missing = $this->schema->invalid( $this->planilhas->getHeader(), 'lojas' ) ) {
                
                // seta os erros
                array_unshift( $missing, '<br><b>Os campos faltantes são:</b>' );
                array_unshift( $missing, 'Caso tenha duvidas de como importar, clique no botão <b>Visualizar exemplo de Importação</b> logo abaixo.' );
                array_unshift( $missing, 'A planilha que você tentou importar não possui todos os campos requeridos.' );
                $this->view->set( 'errors', $missing );
            } else {
                $this->import->cleanTable();
                $this->planilhas->apply( function( $a, $b ) {
                    $this->importar_linha( $a , $b );
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
}

// End of file
