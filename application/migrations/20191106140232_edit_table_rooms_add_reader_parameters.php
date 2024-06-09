<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsAddReaderParameters extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('baud_rate', 'integer', ['null' => FALSE, 'default' => 19200])
            ->update();
    }
}
