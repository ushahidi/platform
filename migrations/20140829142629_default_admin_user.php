<?php

use Phinx\Migration\AbstractMigration;

class DefaultAdminUser extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $query = $this->getAdapter()->getConnection()->prepare(
            'INSERT INTO users (username, password, role, created) VALUES (:username, :password, :role, :created)'
        );
        $query->execute([
            ':username' => 'admin',
            ':password' => '$2y$15$Ha7nHVZApHXfzhrD2HCukuUjjQhIUzPJ0JNWk7KooT6edQFTbeWr6', // password is "admin"
            ':role'     => 'admin',
            ':created'  => time(),
        ]);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("DELETE FROM users WHERE username = 'admin'");
    }
}
