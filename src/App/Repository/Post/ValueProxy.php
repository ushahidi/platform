<?php

/**
 * Ushahidi Post Value Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Post;

use Ushahidi\Core\Usecase\Post\ValuesForPostRepository;

class ValueProxy implements ValuesForPostRepository
{
    protected $factory;
    protected $include_types;

    public function __construct(ValueFactory $factory, array $include_types = [])
    {
        $this->factory = $factory;
        $this->include_types = $include_types;
    }

    // ValuesForPostRepository
    public function getAllForPost(
        $post_id,
        array $include_attributes = [],
        array $exclude_stages = [],
        $excludePrivateValues = true
    ) {
        $results = [];

        $this->factory->each(
            function ($repo) use ($post_id, $include_attributes, &$results, $exclude_stages, $excludePrivateValues) {
                $results = array_merge(
                    $results,
                    $repo->getAllForPost($post_id, $include_attributes, $exclude_stages, $excludePrivateValues)
                );
            },
            $this->include_types
        );

        return $results;
    }

    // ValuesForPostRepository
    public function deleteAllForPost($post_id)
    {
        $total = 0;

        $this->factory->each(function ($repo) use ($post_id, &$total) {
            $total += $repo->deleteAllForPost($post_id);
        });

        return $total;
    }
}
