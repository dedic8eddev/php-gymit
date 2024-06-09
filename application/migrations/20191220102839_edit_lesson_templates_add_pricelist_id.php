<?php

use Phinx\Migration\AbstractMigration;

class EditLessonTemplatesAddPricelistId extends AbstractMigration
{
    public function change()
    {
        $tbl = $this->table('lessons_templates');
        $tbl->addColumn('pricelist_id', 'integer', ['after' => 'room_id', 'null' => TRUE, 'default' => null])
            ->addIndex('pricelist_id',['name' => 'pricelist_id'])
            ->addForeignKey('pricelist_id', 'price_list', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION','constraint' => 'fk_lessons_templates_pricelist_id'])
            ->update();
    }
}
