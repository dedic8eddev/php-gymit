<?php

use Phinx\Migration\AbstractMigration;

class CreateTableGymContractNumbers extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this
            ->table('gym_contract_numbers')
            ->addColumn('gym_contract_prefix', 'string',['limit' => 10])
            ->addColumn('year', 'integer',['limit' => 4])
            ->addColumn('number', 'integer',['limit' => 6, 'default' => 0])
            ->addColumn('contract_number', 'string',['limit' => 20])
            ->addColumn('created_date', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('contract_number', [
                'unique' => TRUE,
                'name' => 'contract_number'
            ])
            ->create()
        ;
    }
}
