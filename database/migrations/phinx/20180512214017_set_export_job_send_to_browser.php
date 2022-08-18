<?php

use Phinx\Migration\AbstractMigration;

class SetExportJobSendToBrowser extends AbstractMigration
{

    public function up()
    {
        $sql = "UPDATE export_job SET send_to_browser=true;";
        $this->execute($sql);
    }
    public function down()
    {
        // TODO : I'm not sure if we should bring this back to false since we have no way of knowing which
        // of the jobs are supposed to have send_to_browser in false/true
    }
}
