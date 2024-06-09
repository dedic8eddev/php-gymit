<?php

use Phinx\Migration\AbstractMigration;

class EditGroupsNameIncreaseLimit extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('groups');
        $tbl->changeColumn('name', 'string',['limit' => 100])
            ->update();
    }
}
