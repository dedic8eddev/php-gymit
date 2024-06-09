<?php

use Phinx\Migration\AbstractMigration;

class AddTableGroupsPermissions extends AbstractMigration
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
            ->table('groups_permissions')
            ->addColumn('group_id', 'integer',['null' => FALSE])
            ->addColumn('permission_section', 'string',['limit' => 50])
            ->addColumn('permission_action', 'string',['limit' => 25])
            ->addIndex('group_id',['name' => 'group_id'])
            ->addForeignKey('group_id', 'groups', 'id', [
                'delete'=> 'CASCADE',
                'update'=> 'NO_ACTION',
                'constraint' => 'fk_groups_permissions_group_id',
            ])
            ->create()
        ;
    }
}
