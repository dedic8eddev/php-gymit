<?php

use Phinx\Migration\AbstractMigration;

class RemoveTableMembershipOverview extends AbstractMigration
{
    public function change()
    {
        $this->table('membership_overview')->drop()->save();
    }
}
