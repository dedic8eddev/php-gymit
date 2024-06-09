<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableCoachDataAddColQuote extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('coach_data');
        $table->addColumn('quote', 'text', ['limit' => MysqlAdapter::TEXT_LONG, 'null' => TRUE, 'default' => NULL, 'after' => 'coach_id'])
            ->update();
    }
}
