<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class ConvertFormTagsToFormAttribute extends AbstractMigration
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
            WHERE `type` = 'post'"
        );
        $tag_rows = $this->fetchAll(
            "SELECT id
            FROM tags"
        );
        $insert = $pdo->prepare(
            "INSERT into form_attributes
            (`label`,`type`, `required`, `priority`, `cardinality`, `input`, `options`, `key`, `form_stage_id`)
            VALUES
            ('Categories', 'varchar', 0, 3, 0, 'tags', :tags, :key, :form_stage_id)"
        );

        $tags = [];

        foreach ($tag_rows as $tag_row) {
            array_push($tags, (int)$tag_row['id']);
        }
        $tags = json_encode($tags);

        foreach ($rows as $row) {
            $uuid = Uuid::uuid4();
            $key = $uuid->toString();
            $insert->execute(
                [
                    ':tags' => $tags,
                    ':key' => $key,
                    ':form_stage_id' => $row['id']
                ]
            );
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
         $this->execute("DELETE from form_attributes where type = 'varchar' AND input = 'tags'");
    }
}
