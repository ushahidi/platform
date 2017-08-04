<?php

use Phinx\Migration\AbstractMigration;

class SetDefaultTaskOnlyForExistingFormStage extends AbstractMigration
{
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();
        $rows = $this->fetchAll("SELECT task_is_internal_only from form_stages");
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET task_is_internal_only = false"
        );
        foreach ($rows as $row) {
            $insert->execute();
        }
    }

    public function down()
    {
        $pdo = $this->getAdapter()->getConnection();
        $rows = $this->fetchAll("SELECT task_is_internal_only from form_stages");
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET task_is_internal_only = true"
        );
        foreach ($rows as $row) {
            $insert->execute();
        }
    }
}
