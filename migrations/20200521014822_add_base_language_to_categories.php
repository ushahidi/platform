<?php

use Phinx\Migration\AbstractMigration;
use Illuminate\Support\Str;

class AddBaseLanguageToCategories extends AbstractMigration
{
    public function up()
    {

        $result = $this->fetchRow(
            "SELECT config_value FROM config WHERE group_name='site' and config_key='language' "
        );
        // get two letter lang code
        $language = Str::before(json_decode($result['config_value']), '-');
        $this->table('tags')
            ->addColumn('base_language', 'string', ['null' => false, 'default' => $language]) //es/en
            ->update();
    }


    public function down()
    {
        $this->table('tags')->removeColumn('base_language');
    }
}
