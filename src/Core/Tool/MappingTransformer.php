<?php

/**
 * Ushahidi Platform User Defined Mapping Transformer
 *
 * A user defined transform, transforms records based on
 * - a source-destination mapping
 * - a set of fixed destination values
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

interface MappingTransformer extends Transformer
{
	public function setMap(Array $map);
	public function setFixedValues(Array $fixedValues);
}
