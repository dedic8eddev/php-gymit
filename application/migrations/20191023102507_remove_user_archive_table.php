<?php

use Phinx\Migration\AbstractMigration;

class RemoveUserArchiveTable extends AbstractMigration
{
    public function change()
    {
        $this->table('users_archive')->drop()->save();
    }
}
