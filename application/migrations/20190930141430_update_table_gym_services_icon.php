<?php


use Phinx\Migration\AbstractMigration;

class UpdateTableGymServicesIcon extends AbstractMigration
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
        $this->getAdapter()->beginTransaction();
        //$delete = $this->execute('ALTER TABLE gym_services DROP CONSTRAINT `fk_gym_services_media_icon_image`');
        $modify = $this->execute('ALTER TABLE gym_services MODIFY COLUMN icon_image varchar(32)');
        $this->getAdapter()->commitTransaction();
    }
}    