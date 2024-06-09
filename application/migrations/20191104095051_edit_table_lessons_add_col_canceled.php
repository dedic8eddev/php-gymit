<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsAddColCanceled extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons');
        $tbl->addColumn('canceled', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('cancel_reason','text', ['limit' => MysqlAdapter::TEXT_TINY, 'null' => TRUE, 'default' => NULL])
            ->update();
    }
}
