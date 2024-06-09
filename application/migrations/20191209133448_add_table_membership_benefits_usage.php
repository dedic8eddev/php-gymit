<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableMembershipBenefitsUsage extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('membership_benefits_usage');
        $tbl->addColumn('benefit_id','integer')
            ->addColumn('client_id','integer')
            ->addColumn('transaction_id','text',['limit'=>255])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex('benefit_id',['name' => 'benefit_id'])
            ->addForeignKey('benefit_id', 'membership_benefits', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_benefits_usage_benefit_id'])
            ->addIndex('client_id',['name' => 'client_id'])
            ->addForeignKey('client_id', 'users', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_benefits_usage_client_id'])

            ->create();
    }
}
