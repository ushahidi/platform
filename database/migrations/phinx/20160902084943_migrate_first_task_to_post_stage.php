<?php

use Phinx\Migration\AbstractMigration;

class MigrateFirstTaskToPostStage extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        // Get first stage for each form
        $rows = $this->fetchAll(
            "SELECT a.id
                FROM form_stages a
                LEFT JOIN form_stages b             -- JOIN for priority
                ON (
                    b.form_id = a.form_id
                    AND b.priority < a.priority
                    )
                LEFT JOIN form_stages c             -- JOIN for priority ties
                ON (
                    c.form_id = a.form_id
                    AND c.priority = a.priority
                    AND c.id < a.id
                    )
                WHERE b.id IS NULL AND c.id IS NULL AND a.form_id NOT IN (
                    SELECT form_id from form_stages WHERE form_stages.type = 'post'
                )"
        );

        $update = $pdo->prepare(
            "UPDATE
                form_stages fs
            SET type = 'post', required = 0
            WHERE id = :id"
        );

        foreach ($rows as $row) {
            $update->execute(
                [
                    ':id' => $row['id']
                ]
            );
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("UPDATE form_stages fs SET type = 'task', required = 1, priority = 0
            WHERE type = 'post'");
    }
}
