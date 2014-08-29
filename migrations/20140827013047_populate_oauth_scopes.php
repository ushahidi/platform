<?php

use Phinx\Migration\AbstractMigration;

class PopulateOauthScopes extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("INSERT INTO oauth_scopes (scope, name)
            VALUES
                ('api', 'api'),
                ('posts', 'posts'),
                ('forms', 'forms'),
                ('sets', 'set'),
                ('tags', 'tags'),
                ('users', 'users'),
                ('media', 'media'),
                ('config', 'config'),
                ('messages', 'messages'),
                ('dataproviders', 'dataproviders'),
                ('stats', 'stats');
        ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM oauth_scopes
            WHERE scope IN (
                'api',
                'posts',
                'forms',
                'sets',
                'tags',
                'users',
                'media',
                'config',
                'messages',
                'dataproviders',
                'stats'
            )
        ");
    }
}
