<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTablePriceListAddColLessonDuration extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('price_list');
        $tbl->dropForeignKey('lesson_id')->save();
        $tbl->removeColumn('lesson_id')
            ->addColumn('lesson_duration', 'time', ['null' => TRUE, 'default' => NULL])
            ->update();
    }
}
