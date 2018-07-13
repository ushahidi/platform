<?php

use Phinx\Migration\AbstractMigration;

class RemoveElevationFromHxlAttributes extends AbstractMigration
{
    /**
     * Remove the 'elevation' HXL attribute.
     * This attribute will be added back if we decide to implement elevation correctly
     * in platform + csv
     */
    public function change()
    {
        $this->execute(
            "DELETE FROM hxl_tag_attributes where attribute_id IN (
                    select id from hxl_attributes where attribute = 'elevation' 
                );"
        );

    }
}
