<?php

use Phinx\Migration\AbstractMigration;

class RenameGroupsToStages extends AbstractMigration
{
    public function up()
    {
        $this->table('form_groups')
            ->rename('form_stages')->save();

        $this->table('form_attributes')->dropForeignKey('form_group_id')->save();

        $table = $this->table('form_attributes');
        $table
            ->renameColumn('form_group_id', 'form_stage_id')
            ->addForeignKey('form_stage_id', 'form_stages', 'id', [
                'delete' => 'CASCADE'
            ])
            ->update();
    }

    public function down()
    {
        $this->table('form_stages')
            ->rename('form_groups')->save();

        $this->table('form_attributes')->dropForeignKey('form_stage_id')->save();

        $table = $this->table('form_attributes');
        $table
            ->renameColumn('form_stage_id', 'form_group_id')
            ->addForeignKey('form_group_id', 'form_groups', 'id', [
                'delete' => 'CASCADE',
            ])
            ->update();
    }
}
