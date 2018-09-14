<?php

use Phinx\Migration\AbstractMigration;

class AddConfidenceScore extends AbstractMigration
{

    public function change()
    {
        $this->table('confidence_scores')
            ->addColumn(
                'score',
                'decimal',
                [
                    'null' => true,
                    'precision' => 11,
                    'scale' => 6,
                ]
            )
            ->addColumn('source', 'string')
            ->addColumn('post_tag_id', 'integer')
            ->addForeignKey('post_tag_id', 'posts_tags', 'id')
            ->create();
    }
}
