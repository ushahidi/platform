<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi CSV Transformer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\MappingTransformer;
use Ushahidi\Core\Entity\PostRepository;
	
class Ushahidi_Transformer_CSVPostTransformer implements MappingTransformer
{
	protected $map;
	protected $fixedValues;
	protected $repo;
	protected $unmapped;

	public function setRepo(PostRepository $repo)
	{
		$this->repo = $repo;
	}

	// MappingTransformer
	public function setMap(Array $map)
	{
		$this->map = $map;
	}

	// MappingTransformer
	public function setFixedValues(Array $fixedValues)
	{
		$this->fixedValues = $fixedValues;
	}

	// Transformer
	public function interact(Array $record)
	{
		$record = array_values($record);
		$columns = $this->map;
		
		// Don't import columns marked as NULL
		foreach ($columns as $index => $column) {
			if ($column === NULL) {
				unset($columns[$index]);
				unset($record[$index]);
			}
		}

		// Remap record columns
		$record = array_combine($columns, $record);

		// Trim
		$record = array_map("trim", $record);

		// Filter post fields from the record
		$post_entity = $this->repo->getEntity();
		$post_fields = array_intersect_key($record, $post_entity->asArray());

		// Remove post fields from the record and leave form values
		foreach ($post_fields as $key => $val) {
			unset($record[$key]);
		}

		// Put values in array
		array_walk($record, function (&$val) {
				$val = [$val];
		});

		$form_values = ['values' => $record];

		return array_merge($post_fields,
						   $form_values,
						   $this->fixedValues);
	}
}
