<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

class CategoryTagMapper implements Mapper
{
    protected $mappingRepo;

    public function __construct(ImportMappingRepository $mappingRepo)
    {
        $this->mappingRepo = $mappingRepo;
    }

    public function __invoke(Import $import, array $input) : array
    {
        $result = new Tag([
            'tag' => $input['category_title'],
            'description' => $input['category_description'] ?? '',
            'color' => $input['category_color'] ?? '',
            'parent_id' => $this->getParent($import->id, $input['parent_id'] ?? 0),
            'role' => $this->getRole($input['category_visible'] ?? 1),
            'priority' => $input['category_position'] ?? 99,
        ]);

        return [
            'result' => $result
        ];

        // NB: We don't map some data ie:
        // - trusted categoried
        // - category icons
    }

    protected function getParent($importId, $parent)
    {
        return $this->mappingRepo->getDestId($importId, 'category', $parent);
    }

    protected function getRole($visible)
    {
        if (!$visible) {
            return ['admin'];
        } else {
            return [];
        }
    }
}
