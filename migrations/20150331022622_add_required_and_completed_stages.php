<?php

use Phinx\Migration\AbstractMigration;

class AddRequiredAndCompletedStages extends AbstractMigration
{
    /**
     * Change
     **/
    public function change()
    {
        // Add required field to form stages
        $this->table('form_stages')
            ->addColumn('required', 'boolean', ['default' => false])
            ->update();

        // Add join table for posts to stages
        $this->table('form_stages_posts', ['id' => false, 'primary_key' => ['form_stage_id', 'post_id']])
            ->addColumn('form_stage_id', 'integer')
            ->addColumn('post_id', 'integer')
            // Include a completed column to mark which stages are complete
            ->addColumn('completed', 'boolean', ['default' => false])
            ->addForeignKey('form_stage_id', 'form_stages', 'id', [
                'delete' => 'CASCADE',
            ])
            ->addForeignKey('post_id', 'posts', 'id', [
                'delete' => 'CASCADE',
            ])
            ->create();
    }
}
