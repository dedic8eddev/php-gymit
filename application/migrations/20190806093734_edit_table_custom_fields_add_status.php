<?php

use Phinx\Migration\AbstractMigration;

class EditTableCustomFieldsAddStatus extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('custom_fields');
        $table->addColumn('hidden','boolean', ['default' => 0])
              ->addColumn('required','boolean', ['default' => 0])
              ->update();
    }
}
