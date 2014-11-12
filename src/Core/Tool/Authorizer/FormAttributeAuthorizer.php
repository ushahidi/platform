<?php

/**
 * Ushahidi Form Attribute Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminOnlyAccess;

// The `FormAttributeAuthorizer` class is responsible
// for access checks on Form Attributes
class FormAttributeAuthorizer implements Authorizer
{
    // only admins are allowed to do anything with form attributes
    use AdminOnlyAccess;
}
