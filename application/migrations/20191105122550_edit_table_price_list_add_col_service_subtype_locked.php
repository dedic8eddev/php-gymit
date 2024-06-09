<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTablePriceListAddColServiceSubtypeLocked extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('price_list');
        $tbl->addColumn('locked', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('service_subtype', 'integer', ['limit' => 4, 'null' => TRUE, 'default' => NULL, 'after' => 'service_type'])
            ->renameColumn('lesson_duration', 'duration')
            ->update();
    }
}
