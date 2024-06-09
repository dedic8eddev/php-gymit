<?php

use Phinx\Migration\AbstractMigration;

class EditTableMembershipPricesAddPurchaseName extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_prices');
        $tbl->addColumn('purchase_name', 'string', ['limit' => 256, 'null' => TRUE, 'default' => NULL, 'after' => 'membership_id'])
            ->update();
    }
}
