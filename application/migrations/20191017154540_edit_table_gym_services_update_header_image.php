<?php

use Phinx\Migration\AbstractMigration;

class EditTableGymServicesUpdateHeaderImage extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('gym_services');
        $table->addColumn('header_image', 'integer', ['default' => null, 'null' => true, 'after' => 'state'])
            ->addForeignKey('header_image', 'media', 'id', ['delete'=> 'SET_NULL', 'update'=> 'NO_ACTION', 'constraint' => 'fk_gym_services_media_header_image'])
            ->update();
    }
}
