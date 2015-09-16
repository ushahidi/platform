<?php

/**
 * Ushahidi Platform Filesystem Adapter Tool
 *
 * Assumes, but does not require, Flysystem: http://flysystem.thephpleague.com/
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2015 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface FilesystemAdapter
{
	public function getAdapter();
}
