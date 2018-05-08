<?php

use Phinx\Migration\AbstractMigration;

class CreateHxlTagsTables extends AbstractMigration
{

	protected $types_tags = [
		'decimal' => [
			'tags' => [
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
				'value',
				'meta',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened'
			]
		],
		'int' => [
			'tags' => [
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
				'value',
				'meta',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened'
			]

		],
		'geometry' => [//TODO: is this mapped to a location input?
			'tags' => [],
			'attributes' => []
		],
		'text' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
		'varchar' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
		'title' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
		'description' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
		'point' => [ //location. What else is location?
			'tags' => [
				'geo',
				'meta'
			],
			'attributes' => [
				'lat',
				'lon',
				'elevation'
			]
		],
		'datetime' => [
			'tags' => [
				'date',
				'meta',

			],
			'attributes' => [
				'start',
				'end',
				'reported',
				'event'
			]
		],
		'media' => [
			'tags' => [
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'meta',
				'status',
				'description'
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
				'reported',
				'event',
			]
		],
		'title' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
		'description' => [
			'tags' => [
				'status',
				'description',
				'meta',
				'beneficiary',
				'item',
				'need',
				'service',
				'impact',
				'loc',
				'region',
				'adm1',
				'adm2',
				'adm3',
				'adm4',
				'adm5',
				'country',
				'org',
				'contact',
				'sector',
				'subsector',
				'activity',
				'output',
				'frequency',
				'capacity',
				'access',
				'operations',
				'value',
				'currency',
				'modality',
				'channel',
				'crisis',
				'event',
				'group',
				'cause',
				'severity',
				'indicator',
				'respondee',
				'population',
				'affected',
				'inneed',
				'targeted',
				'reached',
			],
			'attributes' => [
				'f',
				'm',
				'i',
				'infants',
				'children',
				'adolescents',
				'adults',
				'elderly',
				'start',
				'end',
				'reported',
				'event',
				'killed',
				'injured',
				'infected',
				'displaced',
				'idps',
				'refugees',
				'abducted',
				'threatened',
			]
		],
	];
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
    public function up()
    {

		$this->table('hxl_tags')
			->addColumn('tag_name', 'string', [
				'null' => false,
				'default' => false,
				'comment' => 'The hxl tag. Examples: #geo, #population, #gender'
			])
			->addColumn('description', 'string', [
				'null' => false,
				'default' => false,
				'comment' => 'The hxl tag description.'
			])
			->addIndex(['tag_name'], ['unique' => true])
			->create();


		$this->table('hxl_attributes')
			->addColumn('attribute', 'string', [
				'null' => false,
				'default' => false,
				'comment' => 'The hxl attribute. Examples: +f, +m, +adolescents'
			])
			->addColumn('tag_id', 'integer', [
				'null' => false,
				'default' => false
			])
			->addColumn('description', 'string', [
				'null' => false,
				'default' => false
			])
			->addForeignKey('tag_id', 'hxl_tags', 'id')
			->create();


		$this->table('hxl_tag_attributes')
			->addColumn('form_attribute_type', 'string', [
				'null' => false,
				'default' => false,
				'comment' => 'The form attribute type. Examples: decimal, int, geometry, text, varchar, point'
			])
//			->addColumn('form_attribute_input', 'string', [
//				'null' => false,
//				'default' => false,
//				'comment' => 'The form attribute input. Examples: text, textarea, select, radio, checkbox, file, date, location'
//			])
			->addColumn('hxl_tag_id', 'integer', [
				'null' => false,
				'default' => false
			])
			->addForeignKey('hxl_tag_id', 'hxl_tags', 'id')
			->create();


		// create data for the hxl tables
		$pdo = $this->getAdapter()->getConnection();
		$insert_tag = $pdo->prepare(
			"INSERT IGNORE into
					hxl_tags
					(`tag_name`)
				VALUES (:tag_name)"
		);
		$insert_attribute = $pdo->prepare(
			"INSERT IGNORE into
					hxl_attributes
					(`attribute`, `tag_id`)
				VALUES (:attribute, :tag_id)"
		);

		$insert_tag_attribute_type = $pdo->prepare(
			"INSERT IGNORE into
					hxl_tag_attributes
					(`form_attribute_type`, `hxl_tag_id`)
				VALUES (:form_attribute_type, :hxl_tag_id)"
		);
		foreach ($this->types_tags as $type => $map) {
			foreach ($map['tags'] as $tag) {
				$insert_tag->execute(
					[':tag_name' => $tag]
				);
				$select_tag_id = $pdo->prepare("SELECT id from hxl_tags where tag_name = :tag_name");
				if ($select_tag_id->execute([':tag_name' => $tag])) {
					$tag_id = $select_tag_id->fetch(PDO::FETCH_ASSOC);
					$tag_id = $tag_id['id'];
					// create attributes for the tag
					foreach ($map['attributes'] as $attribute) {
						$insert_attribute->execute(
							[
								':attribute' => $attribute,
								':tag_id'	=> $tag_id
							]
						);
					}
					$insert_tag_attribute_type->execute([':form_attribute_type' => $type, ':hxl_tag_id' => $tag_id]);
				}

			}
		}
    }

    public function down()
	{
		$this->dropTable('hxl_tag_attributes');
		$this->dropTable('hxl_attributes');
		$this->dropTable('hxl_tags');
	}


}
