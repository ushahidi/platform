<?php

namespace Ushahidi\Modules\V5\Commands;

use Illuminate\Database\Eloquent\Model;
use App\Bus\Command\Command;

class CreateCommand implements Command
{
    
     /**
     * @var array
     */
    private $input;

    /**
     * @var Model
     */
    private $model;
   

    public function __construct(array $input)
    {
        $this->setInput($input);
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
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

     /**
     * @return void
     */
    public function setModel(Model $model): void
    {
         $this->model = $model;
    }
}
