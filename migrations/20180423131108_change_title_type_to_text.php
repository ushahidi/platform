<?php

use Phinx\Migration\AbstractMigration;

class ChangeTitleTypeToText extends AbstractMigration
{
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $insert = $pdo->prepare("
            UPDATE form_attributes
            SET input = 'text'
            WHERE type = 'title'
            ;");

        $insert->execute();
    }

    public function down()
    {
    }
}
