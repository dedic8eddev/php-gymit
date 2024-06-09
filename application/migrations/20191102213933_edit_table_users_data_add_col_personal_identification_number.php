<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableUsersDataAddColPersonalIdentificationNumber extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('users_data');
        $tbl->addColumn('personal_identification_number', 'string', ['limit'=>32, 'null' => TRUE, 'default' => NULL, 'after' =>'birth_date', 'comment' => 'Rodné číslo'])
            ->update();
    }
}
