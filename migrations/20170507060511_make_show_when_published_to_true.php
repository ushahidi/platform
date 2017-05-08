<?php

use Phinx\Migration\AbstractMigration;

class MakeShowWhenPublishedToTrue extends AbstractMigration
{
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();
        $rows = $this->fetchAll("SELECT show_when_published from form_stages");
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET show_when_published = true"
        );
        foreach ($rows as $row) {
            $insert->execute();
        }
    }

    public function down()
    {
        $pdo = $this->getAdapter()->getConnection();
        $rows = $this->fetchAll("SELECT show_when_published from form_stages");
        $insert = $pdo->prepare(
            "UPDATE form_stages
            SET show_when_published = false"
        );
        foreach ($rows as $row) {
            $insert->execute();
        }
    }
}
