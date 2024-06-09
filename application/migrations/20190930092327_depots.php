<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class Depots extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('depots');
        $table->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
              ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL])
              ->create();
    }
}
