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
        $this->setInput($input);
        $this->setId($id);
    }


    /**
     * @return array
     */
    public function getInput(): array
    {
        return $this->input;
    }

     /**
     * @return void
     */
    public function setInput(array $input): void
    {
         $this->input = $input;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

     /**
     * @return void
     */
    public function setId(int $id): void
    {
         $this->id = $id;
    }
}
