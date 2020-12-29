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
            $this->table('apikeys')->insert([
                'created' => time(),
                'api_key' => Uuid::uuid4()->toString()
            ])->save();
        }
    }

    public function down()
    {
        // Noop
    }
}
