<?php

use Phinx\Migration\AbstractMigration;

class AddEntityTranslations extends AbstractMigration
{

    public function up()
    {
        $this->table('translations')
            ->addColumn('translatable_type', 'string', ['null' => false]) //form, attribute,stage,category
            ->addColumn('translatable_id', 'integer')
            ->addColumn('translated_key', 'string', ['null' => false]) //name, title, keys
            ->addColumn('translation', 'string', ['null' => false]) //name, title, keys
            ->addColumn('language', 'string', ['null' => false]) //name, title, keys
            ->addTimestamps()
            ->create();
    }
    public function down()
    {
        $this->table('translations')->drop()->save();
    }
}
