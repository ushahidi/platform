<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class UpdatePostTitleDesc extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $rows = $this->fetchAll(
            "SELECT id
                FROM form_stages
                WHERE type = 'post'"
        );

        $insert = $pdo->prepare(
            "INSERT into
                form_attributes
                (`label`, `type`, `required`, `priority`, `cardinality`, `input`, `key`, `form_stage_id`)
            VALUES
                ('Title', 'title', 1, 0, 0, 'varchar', :title_key, :title_form_stage_id),
                ('Description', 'description', 1, 0, 0, 'text', :desc_key, :desc_form_stage_id)"
        );

        foreach ($rows as $row) {
            $uuid = Uuid::uuid4();
            $title_key = $uuid->toString();
            $uuid = Uuid::uuid4();
            $desc_key = $uuid->toString();

            $insert->execute(
                [
                    ':title_form_stage_id' => $row['id'],
                    ':desc_form_stage_id' => $row['id'],
                    ':title_key' => $title_key,
                    ':desc_key' => $desc_key
                ]
            );
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE from form_attributes where type = 'title'");
        $this->execute("DELETE from form_attributes where type = 'description'");
    }
}
