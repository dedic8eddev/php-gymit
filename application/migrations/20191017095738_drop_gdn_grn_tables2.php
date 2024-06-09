<?php

use Phinx\Migration\AbstractMigration;

class DropGdnGrnTables2 extends AbstractMigration
{
    public function change()
    {
        $this->table('depot_grn')->drop()->save();
    }
}
