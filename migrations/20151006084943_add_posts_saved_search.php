<?php

use Phinx\Migration\AbstractMigration;

class AddPostsSavedSearch extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
	
    public function change()
    {
		$this->table('posts_savedsearches', [
			'id' => false,
			'primary_key' => ['post_id', 'set_id'],
		])
			 ->addColumn('post_id', 'integer')
			 ->addColumn('set_id', 'integer')
			 ->create()
			;
    }
}
