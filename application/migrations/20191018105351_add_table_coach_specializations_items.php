<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableCoachSpecializationsItems extends AbstractMigration
{
    public function change()
    {
        $tableSpecItems = $this->table('coach_specializations_items');
        $tableSpecItems->addColumn('name', 'string')
              ->create();
    }
}
