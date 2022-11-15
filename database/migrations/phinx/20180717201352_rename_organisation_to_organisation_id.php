<?php

use Phinx\Migration\AbstractMigration;

class RenameOrganisationToOrganisationId extends AbstractMigration
{
    public function change()
    {
        $this->table('hxl_meta_data')
            ->renameColumn('organisation', 'organisation_id')
            ->update();
    }
}
