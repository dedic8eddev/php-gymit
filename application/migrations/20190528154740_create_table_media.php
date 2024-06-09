<?php


use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateTableMedia extends AbstractMigration
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
        $table = $this->table('media');
        $table->addColumn('name', 'string')
              ->addColumn('file', 'string')
              ->addColumn('file_ext', 'string')
              ->addColumn('meta_tags','text', ['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('created_user','integer')
              ->addColumn('date_last_update', 'timestamp',['default' => 'CURRENT_TIMESTAMP','update' => 'CURRENT_TIMESTAMP'])
              ->addColumn('created_date', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex('created_user',['name' => 'created_user'])
              ->addIndex('name',['unique' => TRUE,'name' => 'name'])
              ->addForeignKey('created_user', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_media_created_user'])
              ->create();
    }
}
