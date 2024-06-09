<?php

use Phinx\Migration\AbstractMigration;

class EditTableUsersDataUpdatePhoto extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users_data');
        $table->changeColumn('photo', 'integer', ['default' => null, 'null' => true, 'after' => 'identification_type'])
            ->addForeignKey('photo', 'media', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION', 'constraint' => 'fk_users_data_media_photo'])
            ->update();
    }
}
