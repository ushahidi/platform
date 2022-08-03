<?php
/**
 * *
 *  * Ushahidi Acl
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace Ushahidi\App\V5\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Ushahidi\App\V5\Events\PostCreatedEvent' => [
            'Ushahidi\App\V5\Listeners\PostCreatedListener',
        ],
        'Ushahidi\App\V5\Events\PostUpdatedEvent' => [
            'Ushahidi\App\V5\Listeners\PostUpdatedListener',
        ],
    ];
}
