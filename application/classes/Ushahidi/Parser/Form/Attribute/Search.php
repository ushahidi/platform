<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Attribute Search Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Usecase\Form\SearchFormAttributeData;
use Ushahidi\Core\Traits\Parser\SortingParser;

class Ushahidi_Parser_Form_Attribute_Search implements Parser
{
    use SortingParser;

    // SortingParser
    private function getDefaultOrderby()
    {
        return 'priority';
    }

    // SortingParser
    private function getAllowedOrderby()
    {
        return ['id', 'priority'];
    }

    // SortingParser
    private function getDefaultOrder()
    {
        return 'asc';
    }

    public function __invoke(Array $data)
    {
        $input = Arr::extract($data, [
            'id', 'created', 'label', 'priority', 'cardinality',
        ]);

        // remove any input with an empty value
        $input = array_filter($input);

        // append sorting data
        $input += $this->getSorting($data);

        return new SearchFormAttributeData($input);
    }
}

