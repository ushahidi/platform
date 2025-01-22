<?php

use Phinx\Migration\AbstractMigration;

class UpdateOauthClientsTableSecret extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->table('oauth_clients')->changeColumn('secret', 'string', ['limit' => 100, 'null' => true])->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->table('oauth_clients')->changeColumn('secret', 'string', ['limit' => 100])->save();
    }
}
