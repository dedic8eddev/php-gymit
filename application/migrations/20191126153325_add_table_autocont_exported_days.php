<?php

use Phinx\Migration\AbstractMigration;

class AddTableAutocontExportedDays extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('autocont_exported_days');
        $tbl->addColumn('club','text')
            ->addColumn('date','date')
            ->create();
    }
}
