<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipAddColData extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('membership');
        $table->addColumn('data', 'text', ['null' => TRUE, 'default' => NULL])
            ->update();
    }
}
