<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi PostSet Listener
 *
 * Listens for new posts that are added to a set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;

class Ushahidi_Listener_Import extends AbstractListener
{

    protected $transformer;
    protected $repo;
	/**
	 * [transform description]
	 * @return [type] [description]
	 */
	protected function transform($record)
	{
		$record = $this->transformer->interact($record);

		return $this->repo->getEntity()->setState($record);
	}

	public function handle(
        EventInterface $event,
        $records=null,
        $csv=null,
        $transformer=null,
        $repo=null,
        $importUsecase=null
    )
	{
        $this->transformer = $transformer;
        $this->repo = $repo;
        $processed = $errors = 0;

        $created_entities = array();
        foreach ($records as $index => $record) {
			// ... transform record
			$entity = $this->transform($record);

			// Ensure that under review is correctly mapped to draft
			if (strcasecmp($entity->status, 'under review')== 0) {
				$entity->setState(['status' => 'draft']);
            }

            if (!service('csv-speedup.enabled')) {
                $importUsecase->verify($entity);
            }
            // ... persist the new entity
            try {
                $id = $this->repo->create($entity);
            } catch (Exception $e) {
                $errors++;
            }
			
			$created_entities[] = $id;
			$processed++;
        }

        $new_status = 'SUCCESS';
        $csv->setState([
            'status' => $new_status,
            'created_ids' => json_encode($created_entities),
			'processed' => $processed,
			'errors' => $errors
        ]);
        service('repository.csv')->update($csv);
    }
}
