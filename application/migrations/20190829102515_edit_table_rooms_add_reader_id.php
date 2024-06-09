<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableRoomsAddReaderId extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rooms');
        $table->addColumn('reader_id', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
              ->update();
    }
}
