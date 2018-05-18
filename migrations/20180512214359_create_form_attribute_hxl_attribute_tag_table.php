<?php

use Phinx\Migration\AbstractMigration;

class CreateFormAttributeHxlAttributeTagTable extends AbstractMigration
{
    public function up()
    {
        $this->table('form_attribute_hxl_attribute_tag')
            ->addColumn('form_attribute_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('hxl_attribute_id', 'integer', [
                'null' => true,
                'default' => null
            ])
            ->addColumn('hxl_tag_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('export_job_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addForeignKey('form_attribute_id', 'form_attributes', 'id', ['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->addForeignKey('hxl_attribute_id', 'hxl_attributes', 'id', ['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->addForeignKey('hxl_tag_id', 'hxl_tags', 'id', ['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->addForeignKey('export_job_id', 'export_job', 'id', ['delete'=>'CASCADE', 'update'=>'CASCADE'])
            ->setOptions(['comment' => 'The link between the form attributes and tags assigned to an export job. 
            This lets us set the hxl header correctly and search for the assigned values'])
            ->create();
    }
    public function down()
    {
        $this->dropTable('form_attribute_hxl_attribute_tag');
    }
}
