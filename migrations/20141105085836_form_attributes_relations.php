<?php

use Phinx\Migration\AbstractMigration;

class FormAttributesRelations extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        // add column form_group_di to form_attributes
        $this->table('form_attributes')
            ->addColumn('form_group_id', 'integer', [
                'default' => null,
                'null' => true,
            ])
            ->addForeignKey('form_group_id', 'form_groups', 'id', [
                'delete' => 'CASCADE',
            ])
            ->update()
            ;

        // migrate all form_attribute <-> form_group relationships
        $connection = $this->getAdapter()->getConnection();
        $relationships = $this->fetchAll('SELECT * FROM form_groups_form_attributes;');
        $prepared_update = $connection->prepare("
            UPDATE form_attributes
            SET form_group_id = :form_group_id
            WHERE id = :id
        ;");

        foreach ($relationships as $r) {
            $prepared_update->execute([
                ':form_group_id' => $r['form_group_id'],
                ':id'            => $r['form_attribute_id'],
            ]);
        }

        // drop the pivot table
        $this->dropTable('form_groups_form_attributes');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // create form_groups_form_attributes
        $this->table('form_groups_form_attributes', [
                'id' => false,
                'primary_key' => ['form_group_id', 'form_attribute_id'],
            ])
            ->addColumn('form_group_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addForeignKey('form_group_id', 'form_groups', 'id')
            ->addForeignKey('form_attribute_id', 'form_attributes', 'id')
            ->create();

        // migrate all form_attribute <-> form_group relationships
        $connection = $this->getAdapter()->getConnection();
        $form_attributes = $this->fetchAll('SELECT * FROM form_attributes;');
        $prepared_insert = $connection->prepare("
            INSERT INTO form_groups_form_attributes (
                form_group_id, form_attribute_id
            ) VALUES (
                :form_group_id, :form_attribute_id
            )
        ;");

        foreach ($form_attributes as $fa) {
            $prepared_insert->execute([
                ':form_group_id'     => $fa['form_group_id'],
                ':form_attribute_id' => $fa['id'],
            ]);
        }

        // drop column in form_attributes
        $this->table('form_attributes')
            ->dropForeignKey('form_group_id')
            ->removeColumn('form_group_id')
            ;
    }
}
