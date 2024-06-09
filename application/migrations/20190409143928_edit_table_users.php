<?php


use Phinx\Migration\AbstractMigration;

class EditTableUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('users');
        $table->addColumn('address', 'text')
              ->update();
    }
}
