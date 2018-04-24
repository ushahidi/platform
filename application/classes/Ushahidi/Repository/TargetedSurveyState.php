<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Form Role Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
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
		return ['post_id', 'contact_id', 'survey_status', 'form_id', 'form_attribute_id', 'message_id'];
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

	public function getActiveByContactId($contact_id)
	{
		return $this->getEntity($this->selectOne([
			'contact_id' => $contact_id,
			'survey_status' => [
				Entity\TargetedSurveyState::PENDING_RESPONSE,
				Entity\TargetedSurveyState::RECEIVED_RESPONSE
			]
		]));
	}

	public function getByForm($form)
	{
		return new Entity\Form($this->selectOne(compact('form')));
	}


	public function setStatusByFormId($form_id, $status)
	{
		return $this->executeUpdate(array('form_id' => $form_id), array('survey_status' => $status));
	}
}
