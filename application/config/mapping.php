<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['Grupo'] = [
    'grupo'  => 'grupo'
];

$config['Rotina'] = [
    'link'          => 'Link',
    'rotina'        => 'Rotina',
    'classificacao' => 'CodClassificacao',
];

$config['Classificacao'] = [
    'nome'   => 'Nome',
    'icone'  => 'Icone',
    'ordem'  => 'Ordem'
];

$config['Estado'] = [
    'nome' => 'Nome',
    'uf'   => 'Uf',
];

$config['Cidade'] = [
    'nome'   => 'Nome',
    'estado' => 'CodEstado',
];

$config['Usuario'] = [
    'uid'   => 'uid',
    'email' => 'email',
    'senha' => 'password',
    'gid'   => 'gid',
];

$config['Cluster'] = [
    'nome' => 'Nome',
    'ref'  => 'Ref'
];

$config['Loja'] = [
    'cluster'        => 'CodCluster',
    'cnpj'           => 'CNPJ',
    'razao'          => 'Razao',
    'nome'           => 'Nome',
    'rede'           => 'CodRede',
    'endereco'       => 'Endereco',
    'numero'         => 'Numero',
    'complemento'    => 'Complemento',
    'bairro'         => 'Bairro',
    'cidade'         => 'CodCidade',
    'estado'         => 'CodEstado',
    'pontosiniciais' => 'PontosIniciais',
    'pontosatuais'   => 'PontosAtuais',
];

$config['Funcionario'] = [
    'loja'        => 'CodLoja',
    'uid'         => 'UID',
    'token'       => 'Token',
    'cargo'       => 'Cargo',
    'nome'        => 'Nome',
    'nascimento'  => 'Nascimento',
    'email'       => 'Email',
    'cpf'         => 'CPF',
    'pontos'      => 'Pontos',
    'endereco'    => 'Endereco',
    'numero'      => 'Numero',
    'complemento' => 'Complemento',
    'cep'         => 'Cep',
    'celular'     => 'Celular',
    'rg'          => 'RG',
    'cidade'      => 'CodCidade',
    'estado'      => 'CodEstado',
    'plataforma'  => 'Plataforma',
    'neoCode'     => 'NeoCode'
];

$config['Categoria'] = [
    'nome' => 'Nome',
    'foto' => 'Foto',
    'ref'  => 'Ref'
];

$config['Produto'] = [
    'basiccode' => 'BasicCode',
    'nome'      => 'Nome',
    'categoria' => 'CodCategoria',
    'descricao' => 'Descricao',
    'foto'      => 'Foto',
    'pontos'    => 'Pontos',
    'video'     => 'Video'
];

$config['Tablet'] = [
    'basiccode' => 'BasicCode',
    'nome'      => 'Nome',
    'descricao' => 'Descricao',
    'foto'      => 'Foto',
    'pontos'    => 'Pontos',
    'video'     => 'Video'
];

$config['Venda_tablet'] = [
    'funcionario' => 'CodFuncionario',
    'quantidade'  => 'Quantidade',
    'tablet'      => 'CodTablet',
    'pontos'      => 'Pontos',
    'data'        => 'Data',
    'loja'        => 'CodLoja',
    'valor'       => 'Valor'  
];

$config['Log'] = [
    'entidade' => 'Entidade',
    'planilha' => 'Planilha',
    'mensagem' => 'Mensagem',
    'status'   => 'Status',
    'data'     => 'Data',
];

$config['Questionario'] = [
    'descricao' => 'Descricao',
    'nome'      => 'Nome',
    'foto'      => 'Foto'
];

$config['Pergunta'] = [
    'resposta'     => 'Resposta',
    'texto'        => 'Texto',
    'pontos'       => 'Pontos',
    'questionario' => 'CodQuestionario',
    'alternativa1' => 'Alternativa1',
    'alternativa2' => 'Alternativa2',
    'alternativa3' => 'Alternativa3',
    'alternativa4' => 'Alternativa4',
];

$config['Resposta'] = [
    'usuario'     => 'CodUsuario',
    'pergunta'    => 'CodPergunta',
    'alternativa' => 'Alternativa',
];

$config['Notificacao'] = [
    'notificacao'   => 'Notificacao',
    'nome'          => 'Nome',
    'disparos'      => 'Disparos',
    'texto'         => 'Texto'
];

$config['Disparo'] = [
    'funcionario'   => 'CodFuncionario',
    'notificacao'   => 'CodNotificacao',
    'data'          => 'Data',
    'status'        => 'Status'
];

$config['Venda'] = [
    'funcionario'   => 'CodFuncionario',
    'quantidade'    => 'Quantidade',
    'produto'       => 'CodProduto',
    'pontos'        => 'Pontos',
    'valor'         => 'Valor',
    'data'          => 'Data',
    'loja'          => 'CodLoja'
];

$config['Treinamento'] = [
    'nome'         => 'Nome',
    'questionario' => 'CodQuestionario',
    'descricao'    => 'Descricao',
    'foto'         => 'Foto',
    'video'        => 'Video'
];

$config['Mensagem'] = [
    'funcionario' => 'CodFuncionario',
    'texto'       => 'Texto',
    'data'        => 'Data'
];

$config['Cartao'] = [
    'funcionario'   => 'CodFuncionario',
    'status'        => 'Status',
    'data'          => 'Data',
    'valor'         => 'Valor',
    'codigo'        => 'Codigo'
];

$config['Parametro'] = [
    'valor' => 'Valor',
    'nome'  => 'Nome',
];

$config['Rede'] = [
    'nome'    => 'Nome',
    'cluster' => 'CodCluster',
    'ref'     => 'Ref'
];

/* end of file */
