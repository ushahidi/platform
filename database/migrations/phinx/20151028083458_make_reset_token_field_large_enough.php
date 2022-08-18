<?php

use Phinx\Migration\AbstractMigration;

class MakeResetTokenFieldLargeEnough extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('user_reset_tokens')
            ->changeColumn('reset_token', 'string', [
                'limit' => 60, // Tokens are 45 chars long, leaving head room though
                ])
            ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('user_reset_tokens')
            ->changeColumn('reset_token', 'string', [
                'limit' => 40,
                ])
            ->update();
    }
}
