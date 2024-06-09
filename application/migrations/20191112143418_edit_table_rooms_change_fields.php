<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsChangeFields extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('personificator', 'boolean', ['null' => false, 'default' => false])
            ->removeColumn('reader_sn')
            ->removeColumn('baud_rate')
            ->update();
    }
}
