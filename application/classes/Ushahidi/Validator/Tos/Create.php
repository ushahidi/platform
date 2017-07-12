<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi CSV Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Validator;
use Ushahidi\Core\Entity\UserRepository;


class Ushahidi_Validator_Tos_Create extends Validator
{
    protected $form_repo;
    protected $default_error_source = 'tos';

    public function __construct(UserRepository $repo)
    {
        $this->user_repo = $repo;
    }

    protected function validate($value)
    {
        if (!Valid::date($value)) {
            return 'date';
        }
    }
    
    protected function getRules()
    {
        return [
            'id' => [
                ['numeric'],
            ],
            'user_id' => [
                ['numeric'],
                [[$this->user_repo, 'exists'], [':value']],
            ],
            'agreement_date' => [
                [[$this, 'validDate'], [':value']],
            ],
            'tos_version_date' => [
                [[$this, 'validDate'], [':value']],
            ],
            
        ];
    }
}