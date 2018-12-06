<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Tag;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportMappingRepository;

class CategoryTagMapper implements Mapper
{
    protected $mappingRepo;

    public function __construct(ImportMappingRepository $mappingRepo)
    {
        $this->mappingRepo = $mappingRepo;
    }

    public function __invoke(array $input) : Entity
    {
        return new Tag([
            'tag' => $input['category_title'],
            'description' => $input['category_description'] ?? '',
            'color' => $input['category_color'] ?? '',
            'parent_id' => $this->getParent($input['parent_id'] ?? 0),
            'role' => $this->getRole($input['category_visible'] ?? 1),
            'priority' => $input['category_position'] ?? 99,
        ]);

        // NB: We don't map some data ie:
        // - trusted categoried
        // - category icons
    }

    protected function getParent($parent)
    {
        return $this->mappingRepo->getDestId('category', $parent);
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
