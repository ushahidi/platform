<?php

use Phinx\Migration\AbstractMigration;

class CreateDefaultSets extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        // @codingStandardsIgnoreStart
        $this->execute(
            "INSERT INTO sets (name, description, filter, view, visible_to, featured, search)
            VALUES
                ( 'All posts', 'All posts', '{ \"status\": \"all\" }', 'list', '[]', 1, 1 ),
                ( 'Published posts', 'All published posts', '{ \"status\": \"published\" }', 'list', '[\"admin\"]', 1, 1 ),
                ( 'Unstructured posts', 'Unstructured posts', '{ \"status\": \"all\", \"form\": \"none\" }', 'list', '[\"admin\"]', 1, 1 ),
                ( 'My posts', 'Your posts', '{ \"user\": \"me\", \"status\": \"all\" }', 'list', '[\"admin\", \"member\"]', 1, 1 )
            "
        );
        // @codingStandardsIgnoreEnd
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute(
            "DELETE FROM sets WHERE name IN ('All posts', 'Published posts', 'Unstructured posts', 'My posts')"
        );
    }
}
