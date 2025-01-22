<?php

use Phinx\Migration\AbstractMigration;

class JoinPostsTagsTableToAttribute extends AbstractMigration
{
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();

        $this->table('posts_tags')
            ->addColumn('id', 'integer', ['null' => false])
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('created', 'integer', ['default' => 0])
            ->update();

        // Manually fix up keys
        $this->execute('ALTER TABLE posts_tags
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (id),
            ADD INDEX (post_id),
            MODIFY COLUMN id INT AUTO_INCREMENT,
            ADD UNIQUE INDEX unique_post_tag_attribute_ids (post_id, tag_id, form_attribute_id)');

        // Make varchar/tags attributes into tags/tags attributes
        $this->execute("
            UPDATE form_attributes
            SET type = 'tags'
            WHERE type = 'varchar' AND input = 'tags'
        ");

        $attributes = $this->fetchAll("
            SELECT form_attributes.id, form_stages.form_id
            FROM form_attributes
            JOIN form_stages ON (form_attributes.form_stage_id = form_stages.id)
            WHERE
                form_attributes.type = 'tags' AND
                form_stages.type = 'post'
            ");

        // Set form_attribute_id for posts_tags entries
        $insert = $pdo->prepare('
                UPDATE posts_tags JOIN posts ON (posts_tags.post_id = posts.id)
                SET form_attribute_id = :attr_id WHERE posts.form_id = :form_id
            ');
        foreach ($attributes as $attribute) {
            $insert->execute([
                ':attr_id' => $attribute['id'],
                ':form_id' => $attribute['form_id']
            ]);
        }

        // Add foreign key for form_attribute_id
        $this->table('posts_tags')
            ->addForeignKey('form_attribute_id', 'form_attributes', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->update();
    }

    public function down()
    {
        // Make tags/tags attributes into varchar/tags attributes
        $this->execute("
            UPDATE form_attributes
            SET type = 'varchar'
            WHERE type = 'tags' AND input = 'tags'
        ");

        // Restore keys/indexs
        $this->execute('ALTER TABLE posts_tags
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (post_id, tag_id),
            MODIFY COLUMN id INT,
            DROP INDEX unique_post_tag_attribute_ids');

        // Remove columns
        $this->table('posts_tags')
            ->dropForeignKey('form_attribute_id')
            ->removeColumn('id')
            ->removeColumn('form_attribute_id')
            ->removeColumn('created')
            ->update();
    }
}
