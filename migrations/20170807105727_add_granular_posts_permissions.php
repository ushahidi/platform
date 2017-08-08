<?php

use Phinx\Migration\AbstractMigration;

class AddGranularPostsPermissions extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO permissions (name, description)
            VALUES
            ('Publish posts', 'Publish posts'),
            ('View any posts', 'View any posts'),
            ('Edit any posts', 'Edit any posts'),
            ('Delete posts', 'Delete posts')
            ");
    }

    public function down()
    {
        $this->execute("DELETE FROM permissions WHERE
            name IN ('Publish posts', 'View any posts', 'Edit any posts', 'Delete posts')");
    }
}
