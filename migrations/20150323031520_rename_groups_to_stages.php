<?php

use Phinx\Migration\AbstractMigration;

class RenameGroupsToStages extends AbstractMigration
{
    public function up()
    {
        $this->table('form_groups')
            ->rename('form_stages');

        $attributes = $this->table('form_attributes');
        $attributes
            ->dropForeignKey('form_group_id')
            ->renameColumn('form_group_id', 'form_stage_id')
            ->update();

        $attributes
            ->addForeignKey('form_stage_id', 'form_stages', 'id', [
                'delete' => 'CASCADE',
            ])
            ->update();

    }

    public function down()
    {
        $this->table('form_stages')
            ->rename('form_groups');

        $attributes = $this->table('form_attributes');
        $attributes
            ->dropForeignKey('form_stage_id')
            ->renameColumn('form_stage_id', 'form_group_id')
            ->update();

        $attributes
            ->addForeignKey('form_group_id', 'form_groups', 'id', [
                 'delete' => 'CASCADE',
            ])
            ->update();
    }
}
