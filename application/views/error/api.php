<?php
/**
 * Ushahidi API Error Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

	echo json_encode(
		array(
			'errors' => array(
				array(
					'message' => $message,
					'code' => $code,
					'class' => $class,
					'file' => $file,
					'line' => $line,
					'trace' => $trace
				)
			)
			)
		);
