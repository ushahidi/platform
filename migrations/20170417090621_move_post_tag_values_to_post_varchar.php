<?php

use Phinx\Migration\AbstractMigration;

class MovePostTagValuesToPostVarchar extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // Don't do this if we haven't already!
        // We have a better way.

        //$pdo = $this->getAdapter()->getConnection();
        // fetching posts with form_attribute_id
        // $posts = $this->fetchAll(
        //     "SELECT posts.id, posts.form_id, form_attributes.id as form_attribute_id
        //     FROM posts
        //     INNER JOIN form_stages
        //     ON form_stages.form_id = posts.form_id
        //     INNER JOIN form_attributes
        //       ON form_attributes.form_stage_id = form_stages.id
        //       AND form_attributes.input = 'tags'
        //       AND form_attributes.type = 'varchar'"
        // );
        // $insert = $pdo->prepare(
        //     "INSERT into
        //         post_varchar
        //         (`post_id`, `form_attribute_id`, `value`, `created`)
        //         VALUES(:post_id, :form_attribute_id, :value, :created)"
        // );
        // foreach ($posts as $post) {
        //         $post_tags = $pdo->prepare(
        //             "SELECT tag_id
        //             FROM posts_tags
        //             WHERE post_id = :post_id"
        //         );
        //         $post_tags->execute([':post_id' => $post['id']]);
        //     // inserting post_ids and tag_ids(value) in post_varchar
        //     $tags = $post_tags->fetchAll();
        //     foreach ($tags as $tag) {
        //         $insert->execute(
        //             [
        //             ':post_id' => $post['id'],
        //             ':form_attribute_id' => $post['form_attribute_id'],
        //             ':value' => $tag['tag_id'],
        //             ':created' => time()
        //             ]
        //         );
        //     }
        // }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute(
            "DELETE from post_varchar
                WHERE form_attribute_id IN
                    (SELECT form_attributes.id FROM form_attributes WHERE input = 'tags' AND type = 'varchar')"
        );
    }
}
