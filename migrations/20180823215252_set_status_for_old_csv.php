<?php

use Phinx\Migration\AbstractMigration;

class SetStatusForOldCsv extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $update = $pdo->prepare(
            "UPDATE csv set status='SUCCESS"
        );
    }
}
