<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Update Form Attribute Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Form\FormAttributeData;

class Ushahidi_Parser_Form_Attribute_Update implements Parser
{
    public function __invoke(Array $data)
    {
        return new FormAttributeData(Arr::extract($data, [
            'id', 'key', 'label', 'input', 'type', 'required', 'default', 'priority',
            'options', 'cardinality', 'form_group_id', 'form_id',
        ]));
    }
}
