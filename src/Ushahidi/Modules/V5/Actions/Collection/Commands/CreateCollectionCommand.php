<?php

namespace Ushahidi\Modules\V5\Actions\Collection\Commands;

use App\Bus\Command\Command;
use Ushahidi\Core\Entity\Set as Collection;

class CreateCollectionCommand implements Command
{
    /**
     * @var Collection
     */
    private $entity;

    public function __construct(Collection $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Collection
     */
    public function getEntity(): Collection
    {
        return $this->entity;
    }
}
