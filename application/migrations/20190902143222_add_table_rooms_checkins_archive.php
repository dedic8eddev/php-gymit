<?php

use Phinx\Migration\AbstractMigration;

class AddTableRoomsCheckinsArchive extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rooms_checkins_archive');
        $table->addColumn('room_id', 'integer')
              ->addColumn('card_id', 'integer')
              ->addColumn('checked_in', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])

              ->addIndex('room_id',['name' => 'room_id'])
              ->addForeignKey('room_id', 'rooms', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_checkins_archive_room_id'])
              ->addIndex('card_id',['name' => 'card_id'])
              ->addForeignKey('card_id', 'users_cards', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_checkins_archive_card_id'])

              ->create();
    }
}
