<?php

/**
 * PHPCS Action for CaptainHook
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Tests
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Tests\CaptainHook;

use SebastianFeldmann\CaptainHook\Config;
use SebastianFeldmann\CaptainHook\Console\IO;
use SebastianFeldmann\CaptainHook\Exception\ActionFailed;
use SebastianFeldmann\CaptainHook\Hook\Action;
use SebastianFeldmann\Cli\Processor\ProcOpen as Processor;
use SebastianFeldmann\Git\Repository;

/**
 * Class PHPCS
 */
class PHPCS implements Action
{
    /**
     * Executes the action.
     *
     * @param  \SebastianFeldmann\CaptainHook\Config         $config
     * @param  \SebastianFeldmann\CaptainHook\Console\IO     $io
     * @param  \SebastianFeldmann\Git\Repository             $repository
     * @param  \SebastianFeldmann\CaptainHook\Config\Action  $action
     * @throws \Exception
     */
    public function execute(Config $config, IO $io, Repository $repository, Config\Action $action)
    {
        $options = $action->getOptions()->getAll();
        $changedPHPFiles = $repository->getIndexOperator()->getStagedFilesOfType('php');

        // If nothing has changed, skip
        if (!$changedPHPFiles) {
            return;
        }

        $io->write('Running PHPCS:', true, IO::VERBOSE);

        $process = new Processor();
        $result  = $process->run(
            'bin/phpcs ' .
            implode(' ', $options) .
            ' ' .
            implode(' ', array_map('escapeshellarg', $changedPHPFiles))
        );

        if (!$result->isSuccessful()) {
            throw ActionFailed::withMessage($result->getStdOut() . PHP_EOL . $result->getStdErr());
        }

        $io->write($result->getStdOut());
    }
}
