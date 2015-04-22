<?php

use Phinx\Migration\AbstractMigration;

class AddSetFields extends AbstractMigration
{
    /**
     * Add new fields to sets
     **/
	public function change()
	{
		$this->table('sets')
			->addColumn('description', 'string', [
				'after' => 'name',
				'default' => '',
			])
			->addColumn('featured', 'boolean', [
				'after' => 'filter',
				'default' => false
			])
			->addColumn('search', 'boolean', [
				'after' => 'filter',
				'default' => false
			])
			->addColumn('visible_to', 'string', [
				'after' => 'filter',
				'limit' => 150,
				'null'  => true
			])
			->addColumn('view_options', 'text', [
				'after' => 'filter',
				'null'  => true
			])
			->addColumn('view', 'string', [
				'after' => 'filter',
				'default' => 'list'
			])
			->addIndex(['featured', 'search'])
			->update();
	}
}
