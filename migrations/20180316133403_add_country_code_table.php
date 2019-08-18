<?php

use Phinx\Migration\AbstractMigration;

class AddCountryCodeTable extends AbstractMigration
{
     /**
     * Migrate Up.
     */
    public function up()
    {
        $this->table('country_codes')
            ->addColumn('country_name', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('dial_code', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addColumn('country_code', 'string', [
                'null' => false,
                'default' => false
            ])
            ->addIndex(['country_code'])
            ->create();

        $pdo = $this->getAdapter()->getConnection();

        $codes = file_get_contents("country_codes.json", true);

        $insert = $pdo->prepare(
            "INSERT into
                country_codes
                (`country_name`, `dial_code`, `country_code`)
            VALUES(:country_name, :dial_code, :country_code)"
        );

        $obj = json_decode($codes, true);

        foreach ($obj as $item) {
            $insert->execute(
                [':country_name' => $item['name'],
                ':dial_code' => $item['dial_code'],
                ':country_code' => $item['code']
                ]
            );
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('country_codes');
    }
}
