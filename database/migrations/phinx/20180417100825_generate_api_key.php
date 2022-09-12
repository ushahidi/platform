<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;

class GenerateApiKey extends AbstractMigration
{
    public function up()
    {
        // fetch a user
        $row = $this->fetchRow('SELECT * FROM apikeys LIMIT 1');

        if (!$row) {
            $this->insert('apikeys', [
                'created' => time(),
                'api_key' => Uuid::uuid4()->toString()
            ]);
        }
    }

    public function down()
    {
        // Noop
    }
}
