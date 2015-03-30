<?php

use Phinx\Migration\AbstractMigration;

class AddPublishedToColumnToPosts extends AbstractMigration
{
    /**
	* Adding role-specific visibility functionality to posts
	* By default, all published posts are public, but post owners
	* or administrators can specify which roles are able to view 
	* a given post if desired.  Admins still have access to all.
     */
	
    public function change()
    {
    	$this->table('posts')
			->addColumn('published_to', 'string', [
				'after' => 'status',
				'limit' => 150,
				'null' => true
			])
			->update();
	
	}
}
