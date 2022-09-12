<?php

use Phinx\Migration\AbstractMigration;

class AddTargetedSurveyFlag extends AbstractMigration
{
    public function up()
    {
        $this->table('forms')
            ->addColumn('targeted_survey', 'boolean', ['default' => false])
            ->update();
    }

    public function down()
    {
        $this->table('forms')
            ->removeColumn('targeted_survey')
            ->update();
    }
}
