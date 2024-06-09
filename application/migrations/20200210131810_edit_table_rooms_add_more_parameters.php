<?php

use Phinx\Migration\AbstractMigration;

class EditTableRoomsAddMoreParameters extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('rooms');
        $table->addColumn('priority', 'integer', ["after" => "address", "default" => 1])
              ->addColumn('exit', 'boolean', ["after" => "entrance", "default" => false])
              ->addColumn('pin_code', 'integer', ['after' => 'personificator', 'null' => true, 'default' => null])
              ->update();
    }
}
