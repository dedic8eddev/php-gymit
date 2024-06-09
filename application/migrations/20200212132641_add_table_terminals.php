<?php

use Phinx\Migration\AbstractMigration;

class AddTableTerminals extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('terminals');
        $tbl->addColumn('terminal_name', 'text') // "Terminál 1"
            ->addColumn('microservice_ip', 'text') // "xx.xx.xx.xx:xxxx"
            ->create();
    }
}
