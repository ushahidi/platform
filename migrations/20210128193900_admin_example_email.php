<?php

use Phinx\Migration\AbstractMigration;

class AdminExampleEmail extends AbstractMigration
{
    public function change()
    {
        $this->execute(
            "UPDATE users SET email='admin@example.com' WHERE email='admin'"
        );
    }
}
