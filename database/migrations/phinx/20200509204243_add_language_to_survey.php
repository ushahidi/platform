<?php

use Illuminate\Support\Str;
use Phinx\Migration\AbstractMigration;

class AddLanguageToSurvey extends AbstractMigration
{

    public function up()
    {
        $result = $this->fetchRow(
            "SELECT config_value FROM config WHERE group_name='site' and config_key='language' "
        );
        // get two letter lang code
        $language = $result ? Str::before(json_decode($result['config_value']), '-') : '';
        $this->table('forms')
            ->addColumn('base_language', 'string', ['null' => false, 'default' => $language]) //es/en
            ->update();
    }

    public function down()
    {
        $this->table('forms')->removeColumn('base_language');
    }
}
