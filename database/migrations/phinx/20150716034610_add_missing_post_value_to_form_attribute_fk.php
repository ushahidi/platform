<?php

use Phinx\Migration\AbstractMigration;

class AddMissingPostValueToFormAttributeFk extends AbstractMigration
{
    /**
     * Add missing fk from post_* -> form_attribute
     */
    public function change()
    {
        $tables = [
            'post_datetime',
            'post_decimal',
            'post_geometry',
            'post_int',
            'post_point',
            'post_text',
            'post_varchar',
        ];

        foreach ($tables as $table) {
            $this->table($table)
                ->addForeignKey('form_attribute_id', 'form_attributes', 'id', [
                    'delete' => 'CASCADE',
                ])
                ->update();
        }
    }
}
