<?php

use Phinx\Migration\AbstractMigration;

class EmbiggenTranslationTextColumnType extends AbstractMigration
{
    public function up()
    {
        // Prior to this migration this column was a VARCHAR(255)
        $this->table('translations')
            ->changeColumn('translation', 'text')
            ->update();
    }

    public function down()
    {
        // this shouldn't be rolled back
    }
}
