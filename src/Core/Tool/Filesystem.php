<?php

/**
 * Ushahidi Platform Filesystem Tool
 *
 * Assumes, but does not require, Flysystem: http://flysystem.thephpleague.com/
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Filesystem
{
	public function putStream($path, $resource, array $config = []);
	public function getSize($path);
	public function getMimetype($path);
}
