<?php

use Phinx\Migration\AbstractMigration;

class SetStatusForOldCsv extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $update = $pdo->prepare(
            "UPDATE csv set status='SUCCESS'"
        );

        $update->execute();
    }
}
