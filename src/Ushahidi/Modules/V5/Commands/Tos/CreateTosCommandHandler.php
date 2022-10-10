<?php

namespace Ushahidi\Modules\V5\Commands\Tos;

use Ushahidi\Modules\V5\Commands\AbstractBaseCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Commands\Tos\CreateTosCommand;
use Ushahidi\Modules\V5\Models\Tos;
use Illuminate\Support\Facades\DB;
use Ushahidi\Modules\V5\Common\Authorize;
use Ushahidi\Modules\V5\Common\Errors;

class CreateTosCommandHandler extends AbstractBaseCommandHandler
{
    use Authorize;
    use Errors;

    /**
     * @param CreateTosCommand $command
     * @return void
     */
    protected function run(Command $command)
    {
        $this->isSupported($command);

      
        $this->authorizeForUser('store', Tos::class);
        $input = $this->setInputDefaults($command->getInput(), 'store', $this->getUser());
        $tos = new Tos();
        if (!$tos->validate($input)) {
            $this->errorInvalidData("Tos", $tos->errors->messages());
        }
        DB::beginTransaction();
        try {
            $tos = tos::create($input);
            DB::commit();
            $command->setModel($tos);
        } catch (\Exception $e) {
            DB::rollback();
            $this->errorDB($e);
        }
    }

    protected function isSupported(Command $command)
    {
        assert(
            get_class($command) === CreateTosCommand::class,
            'Provided command not supported'
        );
    }

    private function setInputDefaults($input, $action, $user)
    {
        if ($action === 'store') {
            $input['user_id'] = $user->id;
            // Save the agreement date to the current time
            $input['agreement_date'] =  time();
        }
        return $input;
    }
}
