<?php

use Phinx\Migration\AbstractMigration;

class EditRoomsAddMoreFlags extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms');
        $tbl->addColumn('wellness', 'boolean', ['after' => 'entrance', 'default' => false])
            ->addColumn('exercise_room', 'boolean', ['after' => 'wellness', 'default' => false])
            ->update();
    }
}
