<?php

use Phinx\Migration\AbstractMigration;

class AddHxlLicenses extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    protected $licenses = [
			[
				'code' => 'CC BY-IGO',
				'name' => 'Creative Commons Attribution for Intergovernmental Organisations',
				'link' => 'https://creativecommons.org/licenses/by/3.0/igo/legalcode'
			],
			[
				'code' => 'CC BY',
				'name' => 'Creative Commons Attribution',
				'link' => 'http://creativecommons.org/licenses/by/4.0/legalcode',
			],
			[
				'code' => 'CC BY-SA',
				'name' => 'Creative Commons Attribution-ShareAlike',
				'link' => 'http://creativecommons.org/licenses/by-sa/4.0/legalcode',
			],
			[
				'code' => 'ODC-ODbL',
				'name' => 'Open Database License',
				'link' => 'http://opendatacommons.org/licenses/odbl/1.0/',
			],
			[
				'code' => 'ODC-BY',
				'name' => 'Open Data Commons Attribution License',
				'link' => 'http://opendatacommons.org/licenses/by/1.0/',
			],
			[
				'code' => 'PDDL',
				'name' => 'Open Data Commons Public Domain Dedication and License',
				'link' => 'https://opendatacommons.org/licenses/pddl/1.0/',
			],

			[
				'code' => 'CC0',
				'name' => 'Public Domain/No restrictions',
				'link' => 'http://creativecommons.org/publicdomain/zero/1.0/legalcode',
			],
			[
				'code' => '',
				'name' => 'Multiple Licenses',
				'link' => ''
			],
			[
				'code' => '',
				'name' => 'Other',
				'link' => ''
			]
		];

	public function up()
    {
		$pdo = $this->getAdapter()->getConnection();

		$insert = $pdo->prepare(
			"INSERT into
                hxl_license
                (`code`, `name`, `link`)
            VALUES
            	(:code, :name, :link)
			"
		);

		foreach ($this->licenses as $item) {
			$insert->execute(
				[
					':code' => $item['code'],
					':link' => $item['link'],
					':name' => $item['name']
				]
			);
		}
    }

    public function down() {
		$pdo = $this->getAdapter()->getConnection();
    	foreach ($this->licenses as $license) {
			$delete = $pdo->prepare(
				"DELETE FROM hxl_license WHERE hxl_license.name = :license_name"
			);
			$delete->execute([
				':license_name' => $license['name']
			]);
		}
	}
}
