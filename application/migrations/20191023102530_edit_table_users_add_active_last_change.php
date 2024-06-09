<?php

use Phinx\Migration\AbstractMigration;

class EditTableUsersAddActiveLastChange extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('active_last_change', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'after' => 'active'])
            ->update();
    }
}
