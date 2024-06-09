<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/../migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/../migration_seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => _ci-sample-config_item['default_database'],
        _ci-sample-config_item['default_database'] => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => _ci-sample-config_item['name'],
            'user' => _ci-sample-config_item['user'],
            'pass' => _ci-sample-config_item['pass'],
            'port' => '3306',
            'charset' => 'utf8',
        ],
        /*'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => _ci-sample-config_item['name'],
            'user' => _ci-sample-config_item['user'],
            'pass' => _ci-sample-config_item['pass'],
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => _ci-sample-config_item['name'],
            'user' => _ci-sample-config_item['user'],
            'pass' => _ci-sample-config_item['pass'],
            'port' => '3306',
            'charset' => 'utf8',
        ]*/
    ],
    'version_order' => 'creation'
];