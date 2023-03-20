<?php

use Phinx\Migration\AbstractMigration;

class EmbiggenPostVarcharValue extends AbstractMigration
{
    public function up()
    {
        // Prior to this migration this column was a VARCHAR(255)
        $this->table('post_varchar')
            ->changeColumn('value', 'string', ['limit' => 8192])
            ->update();
    }

    public function down()
    {
        // this shouldn't be rolled back
    }
}
