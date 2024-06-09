<?php

use Phinx\Migration\AbstractMigration;

class CreateTableMembershipOverview extends AbstractMigration
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
            ->table('membership_overview')
            ->addColumn('user_id', 'integer',['null' => FALSE])
            ->addColumn('membership_id', 'integer',['null' => FALSE])
            ->addColumn('contract_number', 'string',['limit' => 20])
            ->addColumn('price', 'decimal',['precision' => 10,'scale' => 2])
            ->addColumn('membership_from', 'datetime')
            ->addColumn('membership_to', 'datetime')
            ->addIndex('user_id',['name' => 'user_id'])
            ->addIndex('membership_id',['name' => 'membership_id'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_overview_user_id'])
            ->addForeignKey('membership_id', 'membership', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_membership_overview_membership_id'])
            ->create()
        ;
    }
}
