<?php
/**
 * Ushahidi API Error Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

$error = compact('message', 'code', 'class', 'file', 'line', 'trace');
$options = null;

if (Kohana::$environment === Kohana::DEVELOPMENT) {
	$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
}

echo json_encode(['errors' => [$error]], $options);
