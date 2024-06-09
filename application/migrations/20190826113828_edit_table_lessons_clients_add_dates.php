<?php

use Phinx\Migration\AbstractMigration;

class EditTableLessonsClientsAddDates extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('lessons_clients');
        $table->addColumn('lesson_date', 'date', ['null' => true, 'default' => null])
              ->update();
    }
}
