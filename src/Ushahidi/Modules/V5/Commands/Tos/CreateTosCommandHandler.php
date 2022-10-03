<?php

namespace Ushahidi\Modules\V5\Commands\Tos;

use Ushahidi\Modules\V5\Commands\AbstractBaseCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Commands\Tos\CreateTosCommand;
use Ushahidi\Modules\V5\Models\Tos;
use Illuminate\Support\Facades\DB;

class CreateTosCommandHandler extends AbstractBaseCommandHandler
{
    /**
     * @param CreateTosCommand $command
     * @return void
     */
    protected function run(Command $command)
    {
        $this->isSupported($command);

      
        $user = $this->runAuthorizer('store', Tos::class);

        $input = $this->setInputDefaults($command->getInput(), 'store', $user);
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

    private function runAuthorizer($ability, $object)
    {
        $authorizer = service('authorizer.tos');
        // if there's no user the guards will kick them off already, but if there
        // is one we need to check the authorizer to ensure we don't let
        // users without admin perms create forms etc
        // this is an unfortunate problem with using an old version of lumen
        // that doesn't let me do guest user checks without adding more risk.
        $user = $authorizer->getUser();
        if ($user) {
            //$this->authorize($ability, $object);
        }
        return $user;
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
