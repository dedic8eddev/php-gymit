<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddTablePriceList extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('price_list');
        $table->addColumn('lesson_id','integer',['null' => TRUE, 'default' => NULL])
              ->addColumn('name','text',['limit' => MysqlAdapter::TEXT_LONG, 'limit' => 255])
              ->addColumn('period_value','integer',['limit' => MysqlAdapter::INT_TINY,'null' => TRUE, 'default' => NULL])
              ->addColumn('period_type','char',['limit'=>1, 'null' => TRUE, 'default' => NULL, 'comment' => 'mesic (m), den (d), rok (y)'])
              ->addColumn('price','integer',['limit' => 32])
              ->addColumn('description','text',['comment' => 'popis ceny'])             
              ->addForeignKey('lesson_id', 'lessons', 'id', ['delete'=> 'RESTRICT', 'update'=> 'NO_ACTION','constraint' => 'fk_price_list_lesson_id'])
              ->create();
    }
}
