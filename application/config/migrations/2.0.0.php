<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once( '1.0.0.php' );

// Tabela de Produtos
$config['schema']['Tablets'] = [
    'CodTablet' => [
        'type'           => 'int',
        'constraint'     => '11',
        'primary_key'    => TRUE,
        'auto_increment' => TRUE,
    ],
    'BasicCode' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ],
    'Nome' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ],
    'Descricao' => [
        'type' => 'text'
    ],
    'Foto' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ],
    'Pontos' => [
        'type'       => 'int',
        'constraint' => '11',
    ],
    'Video' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ]
];

// Tabela de Vendas
$config['schema']['Vendas_tablet'] = [
    'CodVendaTablet' => [
        'type'           => 'int',
        'constraint'     => '11',
        'primary_key'    => TRUE,
        'auto_increment' => TRUE
    ],
    'CodFuncionario' => [
        'type'       => 'int',
        'constraint' => '11'
    ],
    'CodTablet' => [
        'type'       => 'int',
        'constraint' => '11',
    ],
    'Quantidade' => [
        'type'       => 'int',
        'constraint' => '11',
    ],
    'Pontos' => [
        'type'       => 'int',
        'constraint' => '11',
    ],
    'Data' => [
        'type' => 'datetime'
    ],
    'CodLoja' => [
        'type'       => 'int',
        'constraint' => '11',
    ],
    'Valor' => [
        'type'       => 'float',
        'constraint' => '11,2',
    ]
];

// Tabela de cluster
$config['schema']['Redes'] = [
    'CodRede' => [
        'type'           => 'int',
        'primary_key'    => TRUE,
        'constraint'     => '11',
        'auto_increment' => true
    ],
    'CodCluster' => [
        'type'           => 'int',
        'constraint'     => '11',
        'null'           => true
    ],
    'Nome' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ],
    'Ref' => [
        'type'       => 'varchar',
        'constraint' => '255'
    ]
];

// Tabela de cluster
$config['schema']['imports'] = [
    'id' => [
        'type'           => 'int',
        'primary_key'    => TRUE,
        'constraint'     => '11',
        'auto_increment' => true
    ],
    'line' => [
        'type'       => 'text'
    ],
    'motivo' => [
        'type'       => 'text'
    ]
];

// End of file
