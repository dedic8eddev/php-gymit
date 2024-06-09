<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableCustomFields extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('custom_fields');
        $table->addColumn('name', 'string',['limit' => 255])
              ->addColumn('type','string',['limit' => 255,'null' => TRUE, 'default' => NULL])
              ->addColumn('type_params','text',['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('section','string')
              ->addColumn('description','text',['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->create();
    }
}
