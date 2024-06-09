<?php

use Phinx\Migration\AbstractMigration;

class EditTableUsersRemoveBloat extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('phone')
              ->removeColumn('address')
              ->removeColumn('company')
              ->removeColumn('first_name')
              ->removeColumn('last_name')
              ->update();
    }
}
