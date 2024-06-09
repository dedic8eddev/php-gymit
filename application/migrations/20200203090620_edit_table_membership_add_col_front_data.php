<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipAddColFrontData extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('membership');
        $table->addColumn('front_data', 'text', ['null' => TRUE, 'default' => NULL])
            ->update();
    }
}
