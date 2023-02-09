<?php

use Phinx\Migration\AbstractMigration;

class UpdateDefaultSavedSearches extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("DELETE FROM sets WHERE name = 'All posts' AND filter = '{ \"status\": \"all\" }'");
        $this->execute(
            "UPDATE sets SET
                name = 'Unknown posts',
                description = 'Posts with no assigned survey'
            WHERE name = 'Unstructured posts'"
        );
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute(
            "INSERT INTO `sets` (`name`, `description`, `filter`, `view`, `visible_to`, `search`, `featured`)
            VALUES
                ('All posts', 'All posts', '{ \"status\": \"all\" }', 'list', '[]', 1, 1)
            "
        );
        $this->execute(
            "UPDATE sets SET
                name = 'Unstructured posts',
                description = 'Unstructured posts'
            WHERE name = 'Unknown posts'"
        );
    }
}
