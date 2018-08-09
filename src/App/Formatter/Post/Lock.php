<?php

/**
 * Ushahidi Console Formatter
 *
 * Takes an entity object and returns an array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter\Post;

use Ushahidi\Core\Traits\FormatterAuthorizerMetadata;
use Ushahidi\App\Formatter\API;

class Lock extends API
{
    use FormatterAuthorizerMetadata;
}
