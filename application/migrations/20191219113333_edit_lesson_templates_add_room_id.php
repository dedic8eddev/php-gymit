<?php

use Phinx\Migration\AbstractMigration;

class EditLessonTemplatesAddRoomId extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_templates');
        $tbl->addColumn('room_id', 'integer', ['after' => 'client_limit', 'null' => TRUE, 'default' => null])
            ->addIndex('room_id',['name' => 'room_id'])
            ->addForeignKey('room_id', 'rooms', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_room_id'])
            ->update();
    }
}
