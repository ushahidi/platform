<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;

class UpdateRoleCommand implements Command
{
    /**
     * @var array
     */
    private $input;

    /**
     * @var int
     */
    private $id;
   

    public function __construct(int $id, array $input)
    {
        $this->input = $input ;
        $this->id = $id;
    }


    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->input;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
