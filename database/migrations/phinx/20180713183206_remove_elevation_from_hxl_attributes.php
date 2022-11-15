<?php

use Phinx\Migration\AbstractMigration;

class RemoveElevationFromHxlAttributes extends AbstractMigration
{
    /**
     * Remove the 'elevation' HXL attribute reference.
     * This attribute will be added back if we decide to implement elevation correctly
     * in platform + csv
     */
    public function change()
    {
        $this->execute(
            "DELETE FROM hxl_tag_attributes WHERE attribute_id IN (
                    SELECT id from hxl_attributes WHERE attribute = 'elevation' 
                );"
        );
    }
}
