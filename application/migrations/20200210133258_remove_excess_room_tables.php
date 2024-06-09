<?php

use Phinx\Migration\AbstractMigration;

class RemoveExcessRoomTables extends AbstractMigration
{
    public function change()
    {
        $this->table('rooms_checkins')->drop()->save();
        $this->table('rooms_checkins_archive')->drop()->save();
    }
}
