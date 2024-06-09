<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class EditTableMembershipBenefitsAddPurchaseGift extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_benefits');
        $tbl->addColumn('purchase_gift', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0, 'after' => 'discount'])
            ->update();
    }
}
