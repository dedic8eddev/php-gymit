<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipPricesRemoveColPeriodValue extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_prices');
        $tbl->removeColumn('period_value')->save();
    }
}
