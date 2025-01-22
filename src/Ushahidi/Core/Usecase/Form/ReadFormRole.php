<?php

/**
 * Ushahidi Platform Read Form Role Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Form;

use Ushahidi\Core\Usecase\ReadUsecase;
use Ushahidi\Core\Usecase\Concerns\VerifyFormLoaded;

class ReadFormRole extends ReadUsecase
{
    // - VerifyFormLoaded for checking that the form exists
    use VerifyFormLoaded;
}
