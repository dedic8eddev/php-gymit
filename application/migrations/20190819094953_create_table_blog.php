<?php


use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class CreateTableBlog extends AbstractMigration
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
        $table = $this->table('blog');
        $table->addColumn('name', 'string')
              ->addColumn('author_id', 'integer')
              ->addColumn('state', 'integer',['null' => false, 'default' => 1,'comment' => '1.active,2.inactive'])
              ->addColumn('publish_from', 'timestamp',['null' => true])
              ->addColumn('publish_to', 'timestamp',['null' => true])
              ->addColumn('title', 'string',['null' => TRUE, 'default' => NULL])
              ->addColumn('perex','text', ['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])
              ->addColumn('text','text', ['limit' => MysqlAdapter::TEXT_LONG,'null' => TRUE, 'default' => NULL])              
              ->addColumn('created_date', 'timestamp',['default' => 'CURRENT_TIMESTAMP'])
              ->addIndex('author_id',['name' => 'author_id'])
              ->create();
    }
}
