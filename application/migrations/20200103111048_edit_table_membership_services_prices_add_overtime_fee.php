<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipServicesPricesAddOvertimeFee extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_services_prices');
        $tbl->addColumn('overtime_fee_minutes', 'integer', ['limit' => 4, 'null' => TRUE, 'default' => NULL, 'comment' => 'Every minutes is added overtime_fee_price'])
            ->addColumn('overtime_fee_price', 'decimal', ['precision' => 10,'scale' => 2, 'null' => TRUE, 'default' => NULL, 'after' => 'overtime_fee_minutes', 'comment' => 'Price that is added every overtime_fee_minutes'])
            ->update();
    }
}