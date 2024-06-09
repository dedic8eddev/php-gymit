<?php


use Phinx\Migration\AbstractMigration;

class CreateTableMediaCategory extends AbstractMigration
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
        $table = $this->table('media_category');
        $table->addColumn('media_id', 'integer')
              ->addColumn('category_id', 'integer')
              ->addIndex('media_id',['name' => 'media_id'])
              ->addIndex('category_id',['name' => 'category_id'])
              ->addForeignKey('media_id', 'media', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_media_category_media_id'])
              ->addForeignKey('category_id', 'media_category_items', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_media_category_label_id'])
              ->create();
    }
}
