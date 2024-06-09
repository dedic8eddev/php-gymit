<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableEetCheckouts extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('eet_checkouts');
        $tbl->addColumn('checkout_id','integer')
            ->addColumn('name', 'string', ['limit'=>255])
            ->addColumn('data', 'text', ['null'=>TRUE, 'default'=>NULL])         
            ->create();
    }
}
