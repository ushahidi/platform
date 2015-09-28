<?php
/**
 * Ushahidi API Error Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

$errors = [];
$options = null;

// Build initial error
$error = [
	'status' => $code,
	'title' => $message,
	'message' => $message,
];

// If we're in dev mode
if (Kohana::$environment === Kohana::DEVELOPMENT) {
	// .. generate pretty json
	$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
	// .. and include extra debug info
	$error = $error + compact('class', 'file', 'line', 'trace');
}

// Add first error object..
$errors[] = $error;

// then any additional errors (ie. validation errors)
if (method_exists($e, 'getErrors')) {
	foreach ($e->getErrors() as $key => $value) {
		$errors[] = [
			'status' => $code,
			'title' => $value,
			'message' => $value,
			'source' => [
				'pointer' => "/" . $key
			]
		];
	}
}

// Convert JSON and dump output
echo json_encode(['errors' => $errors], $options);
