<?php

/**
 * Ushahidi Data Provider Authorizer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool\Authorizer;

use Ushahidi\Core\Tool\Authorizer;
use Ushahidi\Core\Traits\AdminOnlyAccess;

// The `DataProviderAuthorizer` class is responsible for access checks on `DataProvider` Entities
class DataProviderAuthorizer implements Authorizer
{
    // only admins are allowed to do anything with data providers
    use AdminOnlyAccess;
}
