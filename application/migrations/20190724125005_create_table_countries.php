<?php


use Phinx\Migration\AbstractMigration;

class CreateTableCountries extends AbstractMigration
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
        $table = $this->table('countries', ['id' => false, 'primary_key' => ['iso']]);
        $table->addColumn('iso', 'char', ['limit' => 2])
              ->addColumn('iso3', 'char', ['limit' => 3, 'null' => true, 'default' => NULL])
              ->addColumn('numcode', 'smallinteger', ['limit' => 6, 'null' => true, 'default' => NULL])
              ->addColumn('name', 'string', ['limit' => 80])
              ->create();
    }
}
