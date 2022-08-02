<?php

/**
 * Ushahidi Platform Delete Form Attribute Use Case
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\V3\Usecase\Form;

use Ushahidi\App\V3\Usecase\DeleteUsecase;
use Ushahidi\App\V3\Usecase\Concerns\VerifyFormLoaded;

class DeleteFormAttribute extends DeleteUsecase
{
    // - VerifyFormLoaded for checking that the form exists
    use VerifyFormLoaded;
}
