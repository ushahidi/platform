<?php

use Phinx\Migration\AbstractMigration;

class SetDefaultTaskOnlyForExistingFormStage extends AbstractMigration
{

    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET task_is_internal_only = false"
        );

        $insert->execute();
    }

    public function down()
    {
        $pdo = $this->getAdapter()->getConnection();
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET task_is_internal_only = true"
        );
        $insert->execute();
    }
}
