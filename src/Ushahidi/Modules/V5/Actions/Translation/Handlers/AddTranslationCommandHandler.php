<?php

namespace Ushahidi\Modules\V5\Actions\Translation\Handlers;

use App\Bus\Action;
use App\Bus\Command\AbstractCommandHandler;
use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Actions\Translation\Commands\AddTranslationCommand;
use Ushahidi\Modules\V5\Repository\Translation\TranslationRepository;

class AddTranslationCommandHandler extends AbstractCommandHandler
{
    private $translationRepository;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    protected function isSupported(Command $command)
    {
        assert($command instanceof AddTranslationCommand);
    }

    /**
     * @param Action|AddTranslationCommand $action
     * @return void
     */
    public function __invoke(Action $action)
    {
        /**
         * @var AddTranslationCommand $action
         */
        $this->isSupported($action);

        $this->translationRepository->store(
            $action->getType(),
            $action->getId(),
            $action->getKey(),
            $action->getTranslation(),
            $action->getLanguage()
        );
    }
}
