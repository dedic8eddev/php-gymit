<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableAutocontExpectedPaymentsAddMoreInfo extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('autocont_expected_payments');
        $tbl->addColumn('last_change','timestamp', ['null' => true, 'default' => null, "after" => "date_created"])
            ->save();
    }
}
