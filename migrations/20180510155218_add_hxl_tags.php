<?php

use Phinx\Migration\AbstractMigration;

class AddHxlTags extends AbstractMigration
{
    private $number_tags = [
        'population',
        'affected',
        'inneed',
        'targeted',
        'reached',
        'value',
        'meta',
    ];
    private $number_attributes = [
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
    ];
    private $text_attributes = [
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
    ];
    private $text_tags = [
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
    ];
    private $types_tags;

    public function up()
    {
        $this->types_tags = [
            'decimal' => [
                'tags' => $this->number_tags,
                'attributes' => $this->number_attributes,
            ],
            'int' => [
                'tags' => $this->number_tags,
                'attributes' => $this->number_attributes,
            ],
            'geometry' => [//FIXME: is this mapped to a location input?
                'tags' => [],
                'attributes' => []
            ],
            'text' => [
                'tags' => $this->text_tags,
                'attributes' => $this->text_attributes,
            ],
            'varchar' => [
                'tags' => $this->text_tags,
                'attributes' => $this->text_attributes,
            ],
            'title' => [
                'tags' => $this->text_tags,
                'attributes' => $this->text_attributes,
            ],
            'description' => [
                'tags' => $this->text_tags,
                'attributes' => $this->text_attributes,
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
        ];

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
					(`attribute`)
				VALUES (:attribute)"
        );

        $insert_tag_attributes = $pdo->prepare(
            "INSERT IGNORE into
					hxl_tag_attributes
					(`tag_id`, `attribute_id`)
				VALUES (:tag_id, :attribute_id)"
        );
        $insert_attribute_types = $pdo->prepare(
            "INSERT IGNORE into
					hxl_attribute_type_tag
					(`form_attribute_type`, `hxl_tag_id`)
				VALUES (:form_attribute_type, :hxl_tag_id)"
        );
        $select_tag_id = $pdo->prepare("SELECT id from hxl_tags where tag_name = :tag_name");
        $select_attribute_id = $pdo->prepare("SELECT id from hxl_attributes where attribute = :attribute");
        foreach ($this->types_tags as $type => $map) {
            foreach ($map['tags'] as $tag) {
                $insert_tag->execute(
                    [':tag_name' => $tag]
                );
                if ($select_tag_id->execute([':tag_name' => $tag])) {
                    $tag_id = $select_tag_id->fetch(PDO::FETCH_ASSOC);
                    $tag_id = $tag_id['id'];
                    // create attributes for the tag
                    foreach ($map['attributes'] as $attribute) {
                        $insert_attribute->execute(
                            [
                                ':attribute' => $attribute,
                            ]
                        );
                        $select_attribute_id->execute([':attribute' => $attribute]);
                        $attribute_id = $select_attribute_id->fetch(PDO::FETCH_ASSOC);
                        $attribute_id = $attribute_id['id'];
                        $insert_tag_attributes->execute([
                            ':tag_id' => $tag_id,
                            ':attribute_id' => $attribute_id
                        ]);
                    }
                    $insert_attribute_types->execute([':form_attribute_type' => $type, ':hxl_tag_id' => $tag_id]);
                }
            }
        }
    }
}
