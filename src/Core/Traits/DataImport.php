<?php

/**
 * Ushahidi DataImport Trait
 *
 * Implements Acl::getRequiredPermissions()
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity;

trait DataImport
{
	// Acl Interface
	protected function getRequiredPermissions()
	{
		return ['Bulk Data Import'];
	}
}
