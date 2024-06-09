<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembershipBenefits extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_benefits');
        $tbl->addColumn('item_id','integer')
            ->addColumn('depot', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('discount', 'integer', ['limit'=>4, 'comment' => 'percentage discount'])
            ->addColumn('quantity','integer')
            ->addColumn('forever', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 0])
            ->addColumn('active', 'boolean',['signed' => FALSE,'null' => FALSE,'default' => 1])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_by','integer')
            ->addForeignKey('created_by', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_benefits_created_by'])
            ->create();
    }
}
