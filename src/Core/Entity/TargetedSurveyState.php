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
	protected $last_sent_form_attribute_id;

	// DataTransformer
	protected function getDefinition()
	{
		$typeColor = function ($color) {
			if ($color) {
				return ltrim($color, '#');
			}
		};
		return [
            'id'          => 'int',
			'form_id'          => 'int',
			'contact_id'       => 'int',
            'last_sent_form_attribute_id'   => 'int',
		];
	}
    protected function getDerived()
    {
        return [
            'contact_id' => ['contact', 'contact.id'],
            'form_id' => ['form', 'form.id'],
            'last_sent_form_attribute_id' => ['form_attribute', 'form_attribute.id'],
        ];
    }
	// Entity
	public function getResource()
	{
		return 'targeted_survey_states';
	}

}
