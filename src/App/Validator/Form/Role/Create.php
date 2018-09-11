<?php

/**
 * Ushahidi Form Role Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form\Role;

use Ushahidi\Core\Entity;

class Create extends Update
{
    protected $default_error_source = 'form_role';
}
