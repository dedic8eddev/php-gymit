<?php

use Phinx\Migration\AbstractMigration;

class EditTableGymServicesUpdateImages extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('gym_services');
        $table->removeColumn('images')
            ->addColumn('cover_image', 'integer', ['default' => null, 'null' => true, 'after' => 'state'])
            ->addColumn('icon_image', 'integer', ['default' => null, 'null' => true, 'after' => 'state'])
            ->addForeignKey('cover_image', 'media', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION', 'constraint' => 'fk_gym_services_media_cover_image'])
            ->update();
    }
}
