<?php

namespace Ushahidi\Modules\V5\Actions\SavedSearch\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\SavedSearch;

class UpdateSavedSearchCommand implements Command
{
    /**
     * @var SavedSearch
     */
    private $entity;

    /**
     * @var int
     */
    private $id;


    public function __construct(int $id, SavedSearch $entity)
    {
        $this->entity = $entity;
        $this->id = $id;
    }


    /**
     * @return SavedSearch
     */
    public function getEntity(): SavedSearch
    {
        return $this->entity;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
