<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create FormAttribute Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Form\FormAttributeData;

class Ushahidi_Parser_Form_Attribute_Create implements Parser
{
    public function __invoke(Array $data)
    {

        if (isset($data['form_group']))
        {
            if (is_array($data['form_group']) && isset($data['form_group']['id']))
            {
                $data['form_group_id'] = $data['form_group']['id'];
            }
            else if (!is_array($data['form_group']))
            {
                $data['form_group_id'] = $data['form_group'];
            }
        }

        if (!isset($data['cardinality']) || $data['cardinality'] === null) {
            $data['cardinality'] = 1;
        }

        $valid = Validation::factory($data)
            ->rules('form_group_id', [
                ['not_empty'],
            ])
            ;

        if (!$valid->check())
        {
            throw new ParserException("Failed to parse form form_attribute create request", $valid->errors('form_attribute'));
        }

        // Ensure that all properties of an FormAttribute entity are defined
        return new FormAttributeData(Arr::extract($data, [
            'key', 'label', 'input', 'type', 'required', 'default', 'priority',
            'options', 'cardinality', 'form_group_id', 'form_id',
        ]));
    }
}

