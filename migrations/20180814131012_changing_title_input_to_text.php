<?php

use Phinx\Migration\AbstractMigration;

class ChangingTitleInputToText extends AbstractMigration
{
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $update = $pdo->prepare(
            "UPDATE form_attributes SET input = 'text' WHERE type = 'title'
                AND input = 'varchar'"
        );

        $update->execute();
    }
}
