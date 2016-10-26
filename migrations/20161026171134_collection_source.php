<?php

use Phinx\Migration\AbstractMigration;

class CollectionSource extends AbstractMigration
{
  /**
   * Migrate Up.
   */
  public function up()
  {
      $this->table('sets')
        ->addColumn('source', 'string', [
            'null' => true,
            ])
        ->update();
  }

  /**
   * Migrate Down.
   */
  public function down()
  {
      $this->table('sets')
        ->removeColumn('source')
        ->update();
  }
}
