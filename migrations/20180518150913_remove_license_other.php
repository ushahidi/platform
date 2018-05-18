<?php

use Phinx\Migration\AbstractMigration;

class RemoveLicenseOther extends AbstractMigration
{
    
    public function change()
    {

        $pdo = $this->getAdapter()->getConnection();

        $delete = $pdo->prepare(
            "DELETE FROM hxl_license WHERE hxl_license.code ='hdx-other'"
        );
        $delete->execute();
    }
}
