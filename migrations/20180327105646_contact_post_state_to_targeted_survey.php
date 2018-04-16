<?php

use Phinx\Migration\AbstractMigration;

class ContactPostStateToTargetedSurvey extends AbstractMigration
{

	/**
	 * Migrate Up.
	 * form_id, post_id, contact_id, and last_sent_attribute_id
	 */
	public function up()
	{
		$this->dropTable('targeted_survey_state');
		$this->table('targeted_survey_state')
			->addColumn('form_id', 'integer', ['null' => false])
			->addColumn('post_id', 'integer', ['null' => false])
			->addColumn('contact_id', 'integer', ['null' => false])
			->addColumn('message_id', 'integer', ['default' => null, 'null' => true])
			->addColumn('form_attribute_id', 'integer', ['null' => true])
			->addColumn('survey_status', 'string', ['null' => false, 'default' => 'PENDING'])
			->addColumn('created', 'integer', ['default' => 0])
			->addColumn('updated', 'integer', ['default' => 0])
			->addForeignKey('form_id', 'forms', 'id', [
				'delete' => 'CASCADE',
				'update' => 'CASCADE',
			])
			->addForeignKey('post_id', 'posts', 'id', [
				'delete' => 'CASCADE',
				'update' => 'CASCADE',
			])
			->addForeignKey('contact_id', 'contacts', 'id', [
				'delete' => 'CASCADE',
				'update' => 'CASCADE',
			])
			->addForeignKey('form_attribute_id', 'form_attributes', 'id', [
				'delete' => 'SET_NULL',
				'update' => 'SET_NULL',
			])

			->addForeignKey('message_id', 'messages', 'id', [
				'delete' => 'SET_NULL',
				'update' => 'SET_NULL',
			])
			->create();
	}

	/**
	 * Migrate Down.
	 */
	public function down()
	{
		$this->dropTable('targeted_survey_state');
		$this->table('targeted_survey_state')
			->addColumn('post_id', 'integer', ['null' => false])
			->addColumn('contact_id', 'integer', ['null' => false])
			->addColumn('status', 'string', ['null' => false, 'default' => 'PENDING'])
			->addColumn('created', 'integer', ['default' => 0])
			->addColumn('updated', 'integer', ['default' => 0])
			->addForeignKey('contact_id', 'contacts', 'id', [
				'delete' => 'CASCADE',
				'update' => 'CASCADE',
			])
			->addForeignKey('post_id', 'posts', 'id', [
				'delete' => 'CASCADE',
				'update' => 'CASCADE',
			])
			->create();
	}
}
