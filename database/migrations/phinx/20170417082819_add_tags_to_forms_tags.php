<?php

use Phinx\Migration\AbstractMigration;

class AddTagsToFormsTags extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $forms = $this->fetchAll(
            "SELECT id
                FROM forms"
        );
        $tags = $this->fetchAll(
            "SELECT id
            FROM tags"
        );
    
        $insert = $pdo->prepare(
            "INSERT into
                forms_tags
                (`tag_id`, `form_id`)
                VALUES(:tag_id, :form_id)
            "
        );

        foreach ($forms as $form) {
            foreach ($tags as $tag) {
                $insert->execute(
                    [':tag_id' => $tag['id'],
                    ':form_id' => $form['id']
                    ]
                );
            }
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE from forms_tags");
    }
}
