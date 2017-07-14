<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Tos Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2017 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Tos;
use Ushahidi\Core\Entity\TosRepository;

class Ushahidi_Repository_Tos extends Ushahidi_Repository implements
    TosRepository
{

    // Ushahidi_Repository
    protected function getTable()
    {
        return 'tos';
    }

    // SearchRepository
    public function getSearchFields()
    {
        return [
            ‘user’
        ];
    }

    public function getEntity(Array $data = null)
    {
        return new Tos($data);
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $user = service('session.user');
        $user_id = $user->id;

        $state = [
            'agreement_date'  => time(),
            'user_id'         => $user_id,
        ];

        return parent::create($entity->setState($state));
    }

}
