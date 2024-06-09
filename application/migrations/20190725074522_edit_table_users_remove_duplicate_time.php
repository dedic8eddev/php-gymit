<?php

use Phinx\Migration\AbstractMigration;

class EditTableUsersRemoveDuplicateTime extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->removeColumn('created_on')
              ->update();
    }
}
