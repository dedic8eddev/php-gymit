<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableDepotItems extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depot_items');
        $table->addColumn('name', 'string',['limit' => 255])
              ->addColumn('number','string',['limit' => 255,'null' => TRUE, 'default' => NULL])
              ->addColumn('price','decimal',['precision' => 10,'scale' => 2])
              ->addColumn('stock','integer')
              ->addColumn('unit','string')
              ->addColumn('description','text',['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('note','text',['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('active', 'boolean',['signed' => FALSE,'null' => TRUE,'default' => 1])
              ->addColumn('last_update', 'timestamp',['default' => 'CURRENT_TIMESTAMP','update' => 'CURRENT_TIMESTAMP'])
              ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->create();
    }
}
