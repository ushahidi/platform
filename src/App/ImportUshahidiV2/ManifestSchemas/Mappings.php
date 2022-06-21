<?php

namespace Ushahidi\App\ImportUshahidiV2\ManifestSchemas;

/*   example YAML representation:

  mappings:
    categories:
    - { from: { id: 1 }, to: { id: 2 } }
    - { from: { id: 2 }, to: { id: 3 } }
    - { from: { id: 3 }, to: { id: 4 } }
    forms:
    - comment: Summary Report
      from: { id: 1 }
      to: { id: 1 }
      incidentColumns: {}
    - comment: Detailed Report
      from: { id: 4 }
      to: { id: 4 }
      incidentColumns: {}
      attributes:
      - { from: { id: 21 }, to: { key: 6ff8d96f-b820-4ac8-a55d-50e3df09e0d5 } }
      - { from: { id: 23 }, to: { key: 86a61869-f4a7-42a2-a19f-3e9806e5c23e } }
*/

use Ushahidi\Core\Entity\TagRespository;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// (Readibility is enhanced in this case by having all the mapping def classes here)

class Mappings
{
    /**
     * @var CategoryMapping[]|null
     */
    public $categories;

    /**
     * @var FormMapping[]|null
     */
    public $forms;
}

class CategoryMapping
{
    /**
     * @var string
     */
    public $comment;

    /**
     * @var MappingEndpoint
     */
    public $from;

    /**
     * @var MappingEndpoint
     */
    public $to;
}

class FormMapping
{
    /**
     * @var string
     */
    public $comment;

    /**
     * @var MappingEndpoint
     */
    public $from;

    /**
     * @var MappingEndpoint
     */
    public $to;

    /**
     * @var IncidentColumnsMapping
     * @required
     */
    public $incidentColumns;

    /**
     * @var AttributeMapping[]|null
     */
    public $attributes;
}

class IncidentColumnsMapping
{
    /**
     * @var MappingEndpoint|null
     */
    public $title;

    /**
     * @var MappingEndpoint|null
     */
    public $description;

    /**
     * @var MappingEndpoint|null
     */
    public $location;

    /**
     * @var MappingEndpoint|null
     */
    public $locationName;

    /**
     * @var MappingEndpoint|null
     */
    public $newsSourceLink;

    /**
     * @var MappingEndpoint|null
     */
    public $videoLink;

    /**
     * @var MappingEndpoint|null
     */
    public $photos;

    /**
     * @var MappingEndpoint|null
     */
    public $categories;

    public function asImportMappings(string $form_id)
    {
        return [
            "{$form_id}-title" => $this->title,
            "{$form_id}-description" => $this->description,
            "{$form_id}-location_name" => $this->locationName,
            "{$form_id}-location" => $this->location,
            "{$form_id}-news_source_link" => $this->newsSourceLink,
            "{$form_id}-video_link" => $this->videoLink,
            "{$form_id}-photos" => $this->photos,
            "{$form_id}-categories" => $this->categories
        ];
    }
}

class AttributeMapping
{
    /**
     * @var MappingEndpoint
     */
    public $from;

    /**
     * @var MappingEndpoint
     */
    public $to;
}

class MappingEndpoint
{
    /**
     * @var string|null
     */
    public $id;

    /**
     * @var string|null
     */
    public $key;
}

// phpcs:enable