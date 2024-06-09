<?php

use Phinx\Migration\AbstractMigration;

class EditTableLessonsRemoveEntryFee extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons');
        $table->removeColumn('entry_fee')
              ->update();
    }
}
