<?php

use Phinx\Migration\AbstractMigration;

class AddFirstAdminToTos extends AbstractMigration
{
   /**
     * Migrate Up.
     */
    public function up()
    {
        $pdo = $this->getAdapter()->getConnection();
        $user = $pdo->query("SELECT * FROM users where id = 1")->fetch();
        // checking if user was created after the tos-feature was built. If not, they still need to sign the tos-agreement.
        if ($user['created'] < strtotime("21 December 2017")) {
        
          // adding first user to tos-table
          $insert = $pdo->prepare('INSERT INTO tos (user_id, agreement_date, tos_version_date) VALUES (:user_id, :agreement_date, :tos_version_date)');
          // setting version-date 1ms before agreement-date
          $insert->execute([
            ':user_id' => $user['id'],
            ':tos_version_date' => time() - 1,
            ':agreement_date' => time()
          ]);
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $pdo = $this->getAdapter()->getConnection();
        $user = $pdo->query("SELECT * FROM users where id = 1")->fetch();
        if ($user['created'] < strtotime("21 December 2017")) {
          // removing first user to tos-table
          $delete = $pdo->prepare("DELETE FROM tos WHERE user_id = :user_id");
          $delete->execute([
            ':user_id' => $user['id']
          ]);
        }
    }
}
