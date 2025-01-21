<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Phinx\Migration\AbstractMigration;

class AddContactIdToPosts extends AbstractMigration
{

    public function change()
    {
        $this->table('posts')
            ->addColumn('contact_id', 'integer', [
                'null' => true,
                'after' => 'author_realname'
            ])
            ->update();
    }
}
