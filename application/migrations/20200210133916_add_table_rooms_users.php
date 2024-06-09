<?php

use Phinx\Migration\AbstractMigration;

class AddTableRoomsUsers extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms_users');
        $tbl->addColumn('user_id', 'integer')
            ->addColumn('room_id', 'integer')

            ->addIndex('user_id',['name' => 'user_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_users_user_id'])            

            ->addIndex('room_id',['name' => 'room_id'])
            ->addForeignKey('room_id', 'rooms', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_users_room_id'])            

            ->create();
    }
}
