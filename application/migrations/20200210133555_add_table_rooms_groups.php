<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableRoomsGroups extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('rooms_groups');
        $tbl->addColumn('group_id', 'integer')
            ->addColumn('room_id', 'integer')

            ->addIndex('group_id',['name' => 'group_id'])
            ->addForeignKey('group_id', 'groups', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_groups_group_id'])            

            ->addIndex('room_id',['name' => 'room_id'])
            ->addForeignKey('room_id', 'rooms', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_rooms_groups_room_id'])            

            ->create();
    }
}
