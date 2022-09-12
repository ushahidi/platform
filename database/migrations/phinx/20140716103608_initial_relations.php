<?php

use Phinx\Migration\AbstractMigration;

class InitialRelations extends AbstractMigration
{
    private $foreign_keys = [
        // Define all foreign keys here, in format:
        // [local table, local column, remote table, remote column]
        ['contacts', 'user_id', 'users', 'id'],
        ['forms', 'parent_id', 'forms', 'id'],
        ['form_groups', 'form_id', 'forms', 'id'],
        ['form_groups_form_attributes', 'form_group_id', 'form_groups', 'id'],
        ['form_groups_form_attributes', 'form_attribute_id', 'form_attributes', 'id'],
        ['messages', 'parent_id', 'messages', 'id'],
        ['posts', 'parent_id', 'posts', 'id'],
        ['posts', 'form_id', 'forms', 'id'],
        ['posts', 'user_id', 'users', 'id'],
        ['posts_media', 'post_id', 'posts', 'id'],
        ['posts_media', 'media_id', 'media', 'id'],
        ['posts_sets', 'post_id', 'posts', 'id'],
        ['posts_sets', 'set_id', 'sets', 'id'],
        ['post_comments', 'parent_id', 'post_comments', 'id'],
        ['post_comments', 'post_id', 'posts', 'id'],
        ['post_datetime', 'post_id', 'posts', 'id'],
        ['post_decimal', 'post_id', 'posts', 'id'],
        ['post_geometry', 'post_id', 'posts', 'id'],
        ['post_int', 'post_id', 'posts', 'id'],
        ['post_point', 'post_id', 'posts', 'id'],
        ['post_text', 'post_id', 'posts', 'id'],
        ['post_varchar', 'post_id', 'posts', 'id'],
        ['sets', 'user_id', 'users', 'id'],
        ['tags', 'parent_id', 'tags', 'id'],
        ['tasks', 'parent_id', 'tasks', 'id'],
        ];

    private $nullable_keys = [
        // Define all foreign keys that can be set to null here:
        ['media', 'user_id', 'users', 'id'],
        ['messages', 'contact_id', 'contacts', 'id'],
        ['messages', 'post_id', 'posts', 'id'],
        ['post_comments', 'user_id', 'users', 'id'],
        ['tasks', 'post_id', 'posts', 'id'],
        ];

    /**
     * Migrate Up.
     */
    public function up()
    {
        foreach ($this->foreign_keys as $key) {
            list($ltable, $lcolumn, $rtable, $rcolumn) = $key;
            try {
                $this->table($ltable)
                     ->addForeignKey($lcolumn, $rtable, $rcolumn, [
                        'delete' => 'CASCADE',
                        'update' => 'RESTRICT',
                        ])
                     ->save();
            } catch (Exception $e) {
                throw new Exception(
                    "Failed to add foreign key: $ltable.$lcolumn -> $rtable.$rcolumn " .
                    $e->getMessage()
                );
            }
        }
        foreach ($this->nullable_keys as $key) {
            list($ltable, $lcolumn, $rtable, $rcolumn) = $key;
            try {
                $this->table($ltable)
                     ->addForeignKey($lcolumn, $rtable, $rcolumn, [
                        'delete' => 'SET_NULL',
                        'update' => 'CASCADE',
                        ])
                     ->save();
            } catch (Exception $e) {
                throw new Exception(
                    "Failed to add nullable foreign key: $ltable.$lcolumn -> $rtable.$rcolumn " .
                    $e->getMessage()
                );
            }
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        foreach ($this->foreign_keys as $key) {
            // For dropping, we only need the local table and column
            list($table, $column) = $key;
            $this->table($table)->dropForeignKey($column);
        }
        foreach ($this->nullable_keys as $key) {
            // For dropping, we only need the local table and column
            list($table, $column) = $key;
            $this->table($table)->dropForeignKey($column);
        }
    }
}
