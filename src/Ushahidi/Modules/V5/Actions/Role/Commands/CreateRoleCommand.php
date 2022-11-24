<?php

namespace Ushahidi\Modules\V5\Actions\Role\Commands;

use App\Bus\Command\Command;

class CreateRoleCommand implements Command
{
    /**
     * @var array
     */
    private $input;

    /**
     * @var int
     */
    private $id;
   
    public function __construct(array $input)
    {
        $this->input = $input;
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
        return  $this->id;
    }

     /**
     * @return void
     */
    public function setId(int $id): void
    {
         $this->id = $id;
    }
}
