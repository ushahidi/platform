<?php

use Phinx\Migration\AbstractMigration;

class RemoveLicenseOther extends AbstractMigration
{
    
    public function change()
    {
        $this->execute(
            "DELETE FROM hxl_license WHERE hxl_license.code = 'hdx-other'"
        );
    }
}
