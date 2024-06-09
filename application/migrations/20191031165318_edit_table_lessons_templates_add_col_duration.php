<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableLessonsTemplatesAddColDuration extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_templates');
        $tbl->removeColumn('base_price')
            ->addColumn('duration', 'time', ['null' => TRUE, 'default' => '01:00:00'])
            ->update();
    }
}
