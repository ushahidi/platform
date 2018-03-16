<?php

use Phinx\Migration\AbstractMigration;

class CreateTargetedSurveyStateTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('targeted_survey_state')
            ->addColumn('form_id', 'integer', ['null' => false])
            ->addColumn('contact_id', 'integer', ['null' => false])
            ->addColumn('current_form_attribute_id', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['default' => 0])
            ->addForeignKey('form_id', 'forms', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->addForeignKey('contact_id', 'contacts', 'id', [
                'delete' => 'CASCADE',
                'update' => 'CASCADE',
                ])
            ->addForeignKey('current_form_attribute_id', 'form_attributes', 'id', [
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
    }
}
