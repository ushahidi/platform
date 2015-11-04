<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi Rate Limiter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\RateLimiter;
use Ushahidi\Core\Entity;

use BehEh\Flaps\Flap;
use BehEh\Flaps\ThrottlingStrategyInterface;

class Ushahidi_RateLimiter implements RateLimiter
{
	/**
	 * @var BehEh\Flaps\Flap
	 */
	protected $flap;

	/**
	 * Sets up a rate limiter with a throttling strategy
	 * @param BehEh\Flaps\Flap $flap
	 * @param BehEh\Flaps\ThrottlingStrategyInterface $trottlingStrategy
	 */
	public function __construct(Flap $flap, ThrottlingStrategyInterface $throttlingStrategy)
    {
        $this->flap = $flap;

		// @todo allow multiple strategies
		$this->flap->pushThrottlingStrategy($throttlingStrategy);
    }

	public function limit(Entity $entity)
	{
		$this->flap->limit($entity->id);
	}
}
