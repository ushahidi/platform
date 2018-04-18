<?php

/**
 * Ushahidi TargetedSurveyState
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2018 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\StaticEntity;

class TargetedSurveyState extends StaticEntity
{
	protected $id;
	protected $form_id;
	protected $contact_id;
	protected $form_attribute_id;
	protected $post_id;
	protected $survey_status;
	protected $message_id;

	const STATUS_INVALID = 'INVALID';
	const PENDING_RESPONSE = 'PENDING RESPONSE';
	const RECEIVED_RESPONSE = 'RECEIVED RESPONSE';
	const SURVEY_FINISHED = 'SURVEY FINISHED';
	const INVALID_CONTACT_MOVED = 'ACTIVE CONTACT IN SURVEY ###';
	
	// DataTransformer
	protected function getDefinition()
	{
		return [
			'id' => 'int',
			'form_id' => 'int',
			'contact_id' => 'int',
			'form_attribute_id' => 'int',
			'post_id' => 'int',
			'survey_status' => 'string',
			'message_id' => 'int'
		];
	}

	protected function getDerived()
	{
		return [
			'contact_id' => ['contact', 'contact.id'],
			'form_id' => ['form', 'form.id'],
			'form_attribute_id' => ['form_attribute', 'form_attribute.id'],
			'post_id' => ['post', 'post.id'],
		];
	}

	// Entity
	public function getResource()
	{
		return 'targeted_survey_states';
	}
}
