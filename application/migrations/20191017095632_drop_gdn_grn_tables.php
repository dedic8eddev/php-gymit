<?php

use Phinx\Migration\AbstractMigration;

class DropGdnGrnTables extends AbstractMigration
{
    public function change()
    {
        $this->table('depot_gdn')->drop()->save();
    }
}
