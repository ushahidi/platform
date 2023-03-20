<?php

use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     */
    public function change()
    {
        $this->table('config')
            ->addColumn('group_name', 'string', ['limit' => 50])
            ->addColumn('config_key', 'string', ['limit' => 50])
            ->addColumn('config_value', 'string')
            ->addColumn('updated', 'timestamp')
            ->addIndex(['group_name', 'config_key'], ['unique' => true])
            ->create();

        $this->table('contacts')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('data_provider', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('type', 'string', [
               'limit' => 20,
               'null' => true,
               'comment' => 'email, phone, twitter',
               ])
            ->addColumn('contact', 'string')
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->addIndex(['data_provider'])
            ->create();

        $this->table('forms')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('name', 'string')
            ->addColumn('description', 'text')
            ->addColumn('type', 'string', [
               'limit' => 30,
               'default' => 'report',
               'comment' => 'report, comment, stream',
               ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();

        $this->table('form_attributes')
            ->addColumn('key', 'string', ['limit' => 150])
            ->addColumn('label', 'string', ['limit' => 150])
            ->addColumn('input', 'string', [
               'limit' => 30,
               'default' => 'text',
               'comment' => 'text, textarea, select, radio, checkbox, file, date, location',
               ])
            ->addColumn('type', 'string', [
               'limit' => 30,
               'default' => 'varchar',
               'comment' => 'decimal, int, geometry, text, varchar, point',
               ])
            ->addColumn('required', 'boolean', ['default' => false])
            ->addColumn('default', 'string', [
               'limit' => 150,
               'null' => true,
               ])
            ->addColumn('priority', 'integer', ['default' => 99])
            ->addColumn('options', 'string', ['null' => true])
            ->addColumn('cardinality', 'integer', [
               'default' => 1,
               'comment' => 'maximum number of values, 0 for unlimited',
               ])
            ->addIndex(['key'], ['unique' => true])
            ->create();

        $this->table('form_groups')
            ->addColumn('form_id', 'integer')
            ->addColumn('label', 'string', ['limit' => 150])
            ->addColumn('priority', 'integer', ['default' => 99])
            ->addColumn('icon', 'string', [
                'limit' => 100,
                'null' => true,
                ])
            ->create();

        $this->table('form_groups_form_attributes', [
                'id' => false,
                'primary_key' => ['form_group_id', 'form_attribute_id'],
                ])
            ->addColumn('form_group_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->create();

        $this->table('media')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('mime', 'string', ['limit' => 50])
            ->addColumn('caption', 'string', ['default' => ''])
            ->addColumn('o_filename', 'string')
            ->addColumn('o_size', 'integer')
            ->addColumn('o_width', 'integer', ['null' => true])
            ->addColumn('o_height', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();

        $this->table('messages')
            ->addColumn('parent_id', 'integer', [
                'null' => true,
                'comment' => 'marks messages being replied to',
                ])
            ->addColumn('contact_id', 'integer', ['null' => true])
            ->addColumn('post_id', 'integer', ['null' => true])
            ->addColumn('data_provider', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('data_provider_message_id', 'string', ['null' => true])
            ->addColumn('title', 'string', ['null' => true])
            ->addColumn('message', 'text')
            ->addColumn('datetime', 'datetime', ['null' => true])
            ->addColumn('type', 'string', [
                'limit' => 20,
                'null' => true,
                'comment' => 'email, phone, twitter',
                ])
            ->addColumn('status', 'string', [
                'limit' => 20,
                'default' => 'pending',
                'comment' => 'pending, received, expired, cancelled, failed, sent',
                ])
            ->addColumn('direction', 'string', [
                'limit' => 20,
                'default' => 'incoming',
                'comment' => 'incoming, outgoing',
                ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addIndex(['data_provider'])
            ->addIndex(['type'])
            ->addIndex(['status'])
            ->addIndex(['direction'])
            ->create();

        $this->table('posts')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('form_id', 'integer', ['null' => true])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('type', 'string', [
               'limit' => 20,
               'default' => 'report',
               'comment' => 'report, update, revision',
               ])
            ->addColumn('title', 'string', ['limit' => 150])
            ->addColumn('slug', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('content', 'text', ['null' => true])
            ->addColumn('status', 'string', [
               'limit' => 20,
               'default' => 'draft',
               'comment' => 'draft, published, pending',
               ])
            ->addColumn('locale', 'string', [
               'limit' => 5,
               'default' => 'en_US',
               ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->addIndex(['type'])
            ->addIndex(['status'])
            ->create();

        $this->table('posts_media', [
                'id' => false,
                'primary_key' => ['post_id', 'media_id'],
                ])
            ->addColumn('post_id', 'integer')
            ->addColumn('media_id', 'integer')
            ->create();

        $this->table('posts_sets', [
                'id' => false,
                'primary_key' => ['post_id', 'set_id'],
                ])
            ->addColumn('post_id', 'integer')
            ->addColumn('set_id', 'integer')
            ->create();

        $this->table('posts_tags', [
                'id' => false,
                'primary_key' => ['post_id', 'tag_id'],
                ])
            ->addColumn('post_id', 'integer')
            ->addColumn('tag_id', 'integer')
            ->create();

        $this->table('post_comments')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('post_id', 'integer')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('content', 'text')
            ->addColumn('status', 'string', [
                'limit' => 20,
                'default' => 'pending',
                'comment' => 'pending, published',
                ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();

        $this->table('post_datetime')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'datetime', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('post_decimal')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'decimal', [
                'precision' => 12,
                'scale' => 4,
                'null' => true,
                ])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        // phinx custom type "geometry"
        $this->table('post_geometry')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'geometry', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('post_int')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        // phinx custom type "point"
        $this->table('post_point')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'point', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('post_text')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'text', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('post_varchar')
            ->addColumn('post_id', 'integer')
            ->addColumn('form_attribute_id', 'integer')
            ->addColumn('value', 'string', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('roles', [
                'id' => false,
                'primary_key' => 'name',
                ])
            ->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('display_name', 'string', ['limit' => 50])
            ->addColumn('description', 'string', ['null' => true])
            ->addColumn('permissions', 'string', ['null' => true])
            ->create();

        $this->table('sets')
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('name', 'string')
            ->addColumn('filter', 'text', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();

        $this->table('tags')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('tag', 'string', ['limit' => 50])
            ->addColumn('slug', 'string', ['limit' => 50])
            ->addColumn('type', 'string', [
                'limit' => 20,
                'default' => 'category',
                'comment' => 'category, status',
                ])
            ->addColumn('color', 'string', [
                'limit' => 6,
                'null' => true,
                ])
            ->addColumn('icon', 'string', [
                'limit' => 20,
                'default' => 'tag',
                ])
            ->addColumn('description', 'text', ['null' => true])
            ->addColumn('role', 'string', ['null' => true])
            ->addColumn('priority', 'integer', ['default' => 99])
            ->addColumn('created', 'integer', ['default' => 0])
            ->create();

        $this->table('tasks')
            ->addColumn('parent_id', 'integer', ['null' => true])
            ->addColumn('post_id', 'integer', ['null' => true])
            ->addColumn('assignee', 'integer', ['null' => true])
            ->addColumn('assignor', 'integer', ['null' => true])
            ->addColumn('description', 'string')
            ->addColumn('status', 'string', [
                'limit' => 20,
                'default' => 'pending',
                'comment' => 'pending, complete, later',
                ])
            ->addColumn('due', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->create();

        $this->table('users')
            ->addColumn('email', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('realname', 'string', [
                'limit' => 150,
                'null' => true,
                ])
            ->addColumn('username', 'string', [
                'limit' => 50,
                'null' => true, /* all of the possible id fields are null? wtf?! */
                ])
            ->addColumn('password', 'string', ['null' => true])
            ->addColumn('role', 'string', [
                'limit' => 50,
                'default' => 'user',
                ])
            ->addColumn('logins', 'integer', ['default' => 0])
            ->addColumn('failed_attempts', 'integer', ['default' => 0])
            ->addColumn('last_login', 'integer', ['null' => true])
            ->addColumn('last_attempt', 'integer', ['null' => true])
            ->addColumn('created', 'integer', ['default' => 0])
            ->addColumn('updated', 'integer', ['null' => true])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }

    /**
     * Migrate Up.
     */
    public function up()
    {
        // noop, uses change()
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // noop, uses change()
    }
}
