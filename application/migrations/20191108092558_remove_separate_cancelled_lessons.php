<?php

use Phinx\Migration\AbstractMigration;

class RemoveSeparateCancelledLessons extends AbstractMigration
{
    public function change()
    {
        $this->table('lessons_cancelled')->drop()->save();
    }
}
