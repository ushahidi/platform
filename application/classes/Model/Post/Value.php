<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Post_Value
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Models
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

abstract class Model_Post_Value extends ORM {
	/**
	 * A post_decimal belongs to a post and form_attribute
	 *
	 * @var array Relationships
	 */
	protected $_belongs_to = array(
		'post' => array(),
		'form_attribute' => array(),
		);

	// Insert/Update Timestamps
	protected $_created_column = array('column' => 'created', 'format' => TRUE);

	/**
	 * Does this attribute type have complex (ie. array) values?
	 * @var boolean
	 **/
	protected $_complex_value = FALSE;

	/**
	 * Rules for the post_decimal model
	 *
	 * @return array Rules
	 */
	public function rules()
	{
		return array(
			'id' => array(
				array('numeric')
			),

			'post_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Post', ':field', ':value')),
			),
			'form_attribute_id' => array(
				array('numeric'),
				array(array($this, 'fk_exists'), array('Form_Attribute', ':field', ':value')),
			),
			'value' => array(

			)
		);
	}

	/**
	 * Does this attribute type have complex values?
	 * @return boolean
	 */
	public function complex_value()
	{
		return $this->_complex_value;
	}

	/**
	 * Load attribute values for specified post
	 *
	 * @param  int $post_id   Post ID
	 * @return [type]         [description]
	 */
	public function load_values_for_post($post_id, &$values)
	{
		$results = $this
				->where('post_id', '=', $post_id)
				->with('form_attribute')
				->find_all();

		// Values stored by 'key' with their post_*.'id'
		$values_with_keys = array();
		foreach($results as $result)
		{
			if (! isset($values_with_keys[$result->form_attribute->key]))
			{
				$values_with_keys[$result->form_attribute->key] = array();
			}
			// Save value and id in multi-value format.
			$values_with_keys[$result->form_attribute->key][] = array(
				'id' => $result->id,
				'value' => $result->value
			);

			// First or single value for attribute
			if (! isset($values[$result->form_attribute->key]) OR
				$result->form_attribute->cardinality == 1 )
			{
				$values[$result->form_attribute->key] = $result->value;
			}
			// Multivalue - use array instead
			else
			{
				$values[$result->form_attribute->key] = $values_with_keys[$result->form_attribute->key];
			}
		}

		return $values;
	}
}