<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableSolariums extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('solariums');
        $tbl->addColumn('name', 'string', ['limit'=>255])
            ->addColumn('usage_minutes_limit', 'biginteger', ['comment' => 'maximum time betweet maintenance (in minutes)'])            
            ->create();
    }
}
