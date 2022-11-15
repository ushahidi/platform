<?php

use Phinx\Migration\AbstractMigration;

class CreateHxlTagsTables extends AbstractMigration
{
    public function up()
    {

        $this->table('hxl_tags')
            ->addColumn('tag_name', 'string', [
                'null' => false,
                'default' => false,
                'comment' => 'The hxl tag. Examples: #geo, #population, #gender'
            ])
            ->addColumn('description', 'string', [
                'null' => false,
                'default' => false,
                'comment' => 'The hxl tag description.'
            ])
            ->addIndex(['tag_name'], ['unique' => true])
            ->create();


        $this->table('hxl_attributes')
            ->addColumn('attribute', 'string', [
                'null' => false,
                'default' => false,
                'comment' => 'The hxl attribute. Examples: +f, +m, +adolescents'
            ])
            ->addColumn('description', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addIndex(['attribute'], ['unique' => true])
            ->create();

        $this->table('hxl_tag_attributes', ['id' => false, 'primary_key' => ['tag_id', 'attribute_id']])
            ->addColumn('tag_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('attribute_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addIndex(['attribute_id', 'tag_id'], ['unique' => true])
            ->addForeignKey('attribute_id', 'hxl_attributes', 'id')
            ->addForeignKey('tag_id', 'hxl_tags', 'id')
            ->create();

        $this->table('hxl_attribute_type_tag')
            ->addColumn('form_attribute_type', 'string', [
                'null' => false,
                'default' => false,
                'comment' => 'The form attribute type. Examples: decimal, int, geometry, text, varchar, point'
            ])
            ->addColumn('hxl_tag_id', 'integer', [
                'null' => false,
                'default' => false
            ])
            ->addForeignKey('hxl_tag_id', 'hxl_tags', 'id')
            ->create();
    }

    public function down()
    {
        $this->dropTable('hxl_tag_attributes');
        $this->dropTable('hxl_attribute_type_tag');
        $this->dropTable('hxl_attributes');
        $this->dropTable('hxl_tags');
    }
}
