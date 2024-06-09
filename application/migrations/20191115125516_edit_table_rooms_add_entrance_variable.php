<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsAddEntranceVariable extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('entrance', 'boolean', ['null' => false, 'default' => false]) // add entrance param (there should always be at least 1 entrance probably)
            ->update();
    }
}
