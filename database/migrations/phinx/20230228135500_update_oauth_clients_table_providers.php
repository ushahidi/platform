<?php

use Phinx\Migration\AbstractMigration;

class UpdateOauthClientsTableProviders extends AbstractMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->table('oauth_clients')->addColumn('provider', 'string', ['after' => 'secret', 'null' => true])->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->table('oauth_clients')->removeColumn('provider');
    }
}
