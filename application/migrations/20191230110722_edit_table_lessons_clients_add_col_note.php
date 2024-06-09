<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsClientsAddColNote extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_clients');
        $tbl->addColumn('note','string', ['null' => true, 'default' => NULL])
            ->addColumn('vip', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0,'after' => 'client_id'])
            ->update();
    }
}
