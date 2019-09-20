<?php

/**
 * Ushahidi Form Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\Form;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\FormRepository;
use Ushahidi\App\Validator\LegacyValidator;
use Ushahidi\App\Facades\Features;

class Update extends LegacyValidator
{
    protected $default_error_source = 'form';
    protected $repo;
    protected $limits;

    /**
     * Construct
     *
     * @param FormRepository  $repo
     */
    public function __construct(FormRepository $repo)
    {
        $this->repo = $repo;
    }

    protected function getRules()
    {
        // Always check validation for name
        $name = $this->validation_engine->getFullData('name');
        if ($name) {
            $data = $this->validation_engine->getData();
            $data['name'] = $name;
            $this->validation_engine->setData($data);
        }
        // End

        return [
            'name' => [
                ['not_empty'],
                ['min_length', [':value', 2]],
                ['regex', [':value', self::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'description' => [['is_string']],
            'color' => [['color']],
            'disabled' => [['in_array', [':value', [true, false]]]],
            'hide_author' => [['in_array', [':value', [true, false]]]],
            'hide_location' => [['in_array', [':value', [true, false]]]],
            'hide_time' => [['in_array', [':value', [true, false]]]],
            'targeted_survey' => [[[$this, 'everyoneCanCreateIsFalse'], [':value', ':fulldata']],]
        ];
    }

    public function checkPostTypeLimit(\Kohana\Validation\Validation $validation)
    {
        $limit = Features::getLimit('forms');
        if ($limit !== INF) {
            $total_forms = $this->repo->getTotalCount();

            if ($total_forms >= $limit) {
                $validation->error('name', 'postTypeLimitReached');
            }
        }
    }

    public function everyoneCanCreateIsFalse($value, $fullData)
    {
        if ($value === true) {
            return $fullData['everyone_can_create'] === false;
        }
        return true;
    }
}
