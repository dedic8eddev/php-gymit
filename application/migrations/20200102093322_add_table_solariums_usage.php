<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTableSolariumsUsage extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('solariums_usage');
        $tbl->addColumn('solarium_id', 'integer')    
            ->addColumn('duration', 'integer')
            ->addColumn('transaction_id','text',['limit'=>255])
            ->addColumn('date_created', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])

            ->addIndex('solarium_id',['name' => 'solarium_id'])
            ->addForeignKey('solarium_id', 'solariums', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_solariums_usage_solarium_id'])            
            ->create();
    }
}
