<?php

use Phinx\Migration\AbstractMigration;

#
# 20170311003829_create_webhook_table.php had a typo:
#       ->addColumn('updated', 'integer', ['default' => 0], ['null' => true])
# should have been
#       ->addColumn('updated', 'integer', ['default' => 0, 'null' => true])
#
# this migration corrects the resulting table definition

class AllowNullInWebhooksUpdated extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('webhooks')
            ->changeColumn('updated', 'integer', ['default' => 0, 'null' => true])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->table('webhooks')
            ->changeColumn('updated', 'integer', ['default' => 0, 'null' => false])
            ->save();
    }
}
