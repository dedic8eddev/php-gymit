<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableUsersDataAddColMultisportId extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('users_data');
        $tbl->addColumn('multisport_id', 'integer', ['null' => TRUE, 'default' => NULL])
            ->update();
    }
}
