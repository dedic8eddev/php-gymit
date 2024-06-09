<?php


use Phinx\Migration\AbstractMigration;

class CreateTableBlogGyms extends AbstractMigration
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
        $table = $this->table('blog_gyms');
        $table->addColumn('blog_id', 'integer')
              ->addColumn('gym_id', 'string')
              ->addIndex('blog_id',['name' => 'blog_id'])
              ->addIndex('gym_id',['name' => 'gym_id'])
              ->addForeignKey('blog_id', 'blog', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_blog_blog_id'])
              ->create();
    }
}
