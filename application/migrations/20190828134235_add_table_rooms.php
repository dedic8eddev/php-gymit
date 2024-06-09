<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableRooms extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rooms');
        $table->addColumn('name', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
              ->addColumn('description', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
              ->create();
    }
}
