<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * FrontlineSMS Error Template
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\FrontlineSms
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

	echo json_encode(
		array(
			'payload' => array(
				'success' => FALSE,
				'error' => $message,
				'code' => $code,
				'class' => $class,
				'file' => $file,
				'line' => $line,
			)
		)
	);
