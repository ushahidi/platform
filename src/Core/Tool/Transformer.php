<?php

/**
 * Ushahidi Platform Transformer Tool
 *
 * Transform a record from one format to another
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface Transformer
{
	public function interact(Array $data);
}
