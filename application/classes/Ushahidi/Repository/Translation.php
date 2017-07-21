<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Config Repository, using Kohana::$config
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Data;
use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\TranslationRepository;
use Ushahidi\Core\Exception\NotFoundException;

use League\Event\ListenerInterface;
use Ushahidi\Core\Traits\Event;

class Ushahidi_Repository_Translation implements
	TranslationRepository
{

	protected $db;

	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	// TranslationRepository
	public function saveTranslation($resource, $resourceId, $property, $translation, $source, $locale)
	{
		$query = DB::query(Database::INSERT, "
			INSERT INTO `translations`
			(`resource`, `resource_id`, `property`, `translation`, `source`, `locale`) VALUES (:resource, :resourceId, :property, :translation, :source, :locale)
			ON DUPLICATE KEY UPDATE `translation` = :translation, `source` = :source;
		")
		->parameters([
			':resource' => $resource,
			':resourceId' => $resourceId,
			':property' => $property,
			':translation' => $translation,
			':source' => $source,
			':locale' => $locale
		]);

		list($id) = $query->execute($this->db);
		return $id;
	}

	public function getTranslations($resource, $resourceId)
	{
		$translations = DB::select('property', 'translation', 'locale')->from('translations')
				->where('resource', '=', $resource)
				->where('resource_id', '=', $resourceId)
				->execute($this->db)
				->as_array();

		$locales = [];
		foreach ( $translations as $translation ) {
		    $locales[$translation['locale']][$translation['property']] = $translation['translation'];
		}
		return $locales;
	}

}

