<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\TargetedSurveyStateRepository;

class Ushahidi_Repository_TargetedSurveyState extends Ushahidi_Repository implements
	TargetedSurveyStateRepository
{
	// Ushahidi_Repository
	protected function getTable()
	{
		return 'targeted_survey_state';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		return new Entity\TargetedSurveyState($data);
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['form_id', 'contact_id', 'last_sent_form_attribute_id'];
	}

	// ContactRepository
	public function getByContact($contact, $type)
	{
		return $this->getEntity($this->selectOne(compact('contact', 'type')));
	}

	public function getByPost($post)
	{
		return new Entity\Post($this->selectOne(compact('post')));
	}
}
