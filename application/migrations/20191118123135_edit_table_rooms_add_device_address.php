<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsAddDeviceAddress extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('address', 'integer', ['null' => false, 'default' => 0]) // ADDRESS / 1-255, defines the calling interface
            ->update();
    }
}
