<?php

use Phinx\Migration\AbstractMigration;

class AddBaseLanguageToPosts extends AbstractMigration
{
    public function up()
    {

        $result = $this->fetchRow(
            "SELECT config_value FROM config WHERE group_name='site' and config_key='language' "
        );
        // get two letter lang code
        $language = str_before(json_decode($result['config_value']), '-');
        $this->table('posts')
            ->addColumn('base_language', 'string', ['null' => false, 'default' => $language]) //es/en
            ->update();
    }


    public function down()
    {
        $this->table('posts')->removeColumn('base_language');
    }
}
