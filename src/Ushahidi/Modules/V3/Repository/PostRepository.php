<?php

/**
 * Ushahidi Post Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Repository;

use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Contracts\Entity;
use Ushahidi\Core\Entity\Post;
use Ushahidi\Core\Entity\Media;
use Ushahidi\Core\Concerns\Event;
use Illuminate\Support\Collection;
use Ushahidi\Core\Entity\PostLock;
use Ushahidi\Core\Tool\SearchData;
use Ushahidi\Core\Tool\BoundingBox;
use Ushahidi\Core\Concerns\UserContext;
use Ushahidi\Core\Tool\OhanzeeResolver;
use Ushahidi\Core\Tool\Permissions\InteractsWithPostPermissions;
use Ushahidi\Modules\V3\Repository\Post\ValueFactory as PostValueFactory;
use Ushahidi\Contracts\Repository\Usecase\SetPostRepository;
use Ushahidi\Contracts\Repository\Usecase\UpdatePostRepository;
use Ushahidi\Contracts\Repository\Entity\ContactRepository;
use Ushahidi\Contracts\Repository\Entity\PostLockRepository;
use Ushahidi\Contracts\Repository\Entity\FormRepository as FormRepositoryContract;
use Ushahidi\Contracts\Repository\Entity\PostRepository as PostRepositoryContract;
use Ushahidi\Contracts\Repository\Entity\FormStageRepository as FormStageRepositoryContract;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository as FormAttributeRepositoryContract;
use Ushahidi\Contracts\Search;

class PostRepository extends OhanzeeRepository implements
    PostRepositoryContract,
    UpdatePostRepository,
    SetPostRepository
{
    use UserContext;

    // Use Event trait to trigger events
    use Event;

    // Use the JSON transcoder to encode properties
    use Concerns\JsonTranscode;

    // Provides `postPermissions`
    use InteractsWithPostPermissions;

    use Concerns\UsesBulkAutoIncrement;

    protected $form_attribute_repo;
    protected $form_stage_repo;
    protected $form_repo;
    protected $contact_repo;
    protected $post_lock_repo;

    protected $post_value_factory;
    protected $bounding_box_factory;
    // By default remove all private responses
    protected $restricted = true;

    protected $form_attributes_by_form;
    protected $form_attributes_by_key;

    protected $include_value_types = [];
    protected $include_attributes = [];
    protected $exclude_stages = [];

    protected $search_output_type = 'full';

    // Map of values that can be provided as $data to getEntity(),
    // in order to save time looking them up in their corresponding value repo
    protected $data_to_entity_value_mappings = [
        [
            'repo' => 'point',
            'attribute_key' => 'point_attribute_key',
            'value' => 'point_value',
            'obtained_type' => 'point'
        ],
        [
            'repo' => 'geometry',
            'attribute_key' => 'geometry_attribute_key',
            'value' => 'geometry_value',
            'obtained_type' => 'geometry'
        ]
    ];

    protected $listener;

    public function __construct(
        OhanzeeResolver $resolver,
        FormAttributeRepositoryContract $form_attribute_repo,
        FormStageRepositoryContract $form_stage_repo,
        FormRepositoryContract $form_repo,
        PostLockRepository $post_lock_repo,
        ContactRepository $contact_repo,
        PostValueFactory $post_value_factory,
        \Aura\Di\Injection\Factory $bounding_box_factory
    ) {

        parent::__construct($resolver);

        $this->form_attribute_repo = $form_attribute_repo;
        $this->form_stage_repo = $form_stage_repo;
        $this->form_repo = $form_repo;
        $this->post_lock_repo = $post_lock_repo;
        $this->contact_repo = $contact_repo;
        $this->post_value_factory = $post_value_factory;
        $this->bounding_box_factory = $bounding_box_factory;
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'posts';
    }

    protected function fetchFormAttributes($form_ids = []): void
    {
        $form_ids = is_array($form_ids) ? $form_ids : [ $form_ids ];

        $attrs = $this->form_repo->getAllFormStagesAttributes($form_ids);

        $this->form_attributes_by_form = $attrs->groupBy('form_id');
        $this->form_attributes_by_key = $attrs->keyBy('key');
    }

    // OhanzeeRepository
    public function getEntity(array $data = null): Post
    {
        // Ensure we are dealing with a structured Post

        $user = $this->getUser();
        $excludePrivateValues = true;
        $excludeStages = [];
        $values = $data['values'] ?? [];

        // Check post permissions
        // @todo move or double up in formatter. That should enforce what users can see
        $excludePrivateValues = !$this->postPermissions->canUserReadPrivateValues(
            $user
        );

        $this->post_value_factory->getRepo('point')->hideLocation(
            !$this->postPermissions->canUserSeeLocation(
                $user,
                new Post($data),
                $this->form_repo
            )
        );

        if (!empty($data['form_id'])) {
            // Get Hidden Stage Ids to be excluded from results
            $excludeStages = $this->form_stage_repo->getHiddenStageIds(
                $data['form_id'],
                $data['status']
            );
        }

        if (!empty($data['id'])) {
            // NOTE: This and the restriction above belong somewhere else,
            // ideally in their own step
            // Check if time info should be returned
            if (!$this->postPermissions->canUserSeeTime($user, new Post($data), $this->form_repo)) {
                // Hide time on survey fields
                $this->post_value_factory->getRepo('datetime')->hideTime(true);

                // @todo move to formatter. That where this normally happens
                // Replace time with 00:00:00
                if ($postDate = date_create($data['post_date'], new \DateTimeZone('UTC'))) {
                    $data['post_date'] = $postDate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
                }
                if ($created = date_create('@'.$data['created'], new \DateTimeZone('UTC'))) {
                    $data['created'] = $created->setTime(0, 0, 0)->format('U');
                }
                if ($updated = date_create('@'.$data['updated'], new \DateTimeZone('UTC'))) {
                    $data['updated'] = $updated->setTime(0, 0, 0)->format('U');
                }
            }

            if (!$this->postPermissions->canUserSeeAuthor($user, new Post($data), $this->form_repo)
                && ($user->id != $data['user_id'])
                && ($data['author_realname'] || $data['user_id'] || $data['author_email'])) {
                // @todo move to formatter. That where this normally happens
                unset($data['author_realname']);
                unset($data['author_email']);
                unset($data['user_id']);
            }

            /* -- VALUES HANDLING -- */

            /* handle values already carried in in the $data object */
            $already_obtained_types = [];
            foreach ($this->data_to_entity_value_mappings as $mapping) {
                // Check if value should be visible
                $attribute_key = $data[$mapping['attribute_key']] ?? null;
                $attribute_value = $data[$mapping['value']] ?? null;

                // Skip if data not provided
                if (!$attribute_key || !$attribute_value) {
                    continue;
                }

                // Check visibility
                $attribute = $this->form_attributes_by_key->get($attribute_key);
                // .. exclude values marked as private
                if ($excludePrivateValues && $attribute['response_private']) {
                    continue;
                }
                // .. exclude stages
                if ($excludeStages && in_array($attribute['form_stage_id'], $excludeStages)) {
                    continue;
                }
                // .. exclude non-mentioned attributes
                if ($this->include_attributes && !in_array($attribute_key, $this->include_attributes)) {
                    continue;
                }

                // Build and set values
                $value = $this->post_value_factory
                    ->getRepo($mapping['repo'])
                    ->getEntity(['value' => $attribute_value]);
                $values[$attribute_key] = [ $value->value ];

                $already_obtained_types = array_merge($already_obtained_types, [ $mapping['obtained_type'] ]);

                // Unset original values
                unset($data[$mapping['attribute_key']]);
                unset($data[$mapping['value']]);
            }

            // Obtain the rest of the requested values
            $other_values = [];
            $types_to_fetch = null;

            if ($this->form_attributes_by_form) {
                /* Check which types are used in the form */
                $form_attributes = $this->form_attributes_by_form->get(intval($data['form_id']));
                if ($form_attributes) {
                    /* Count how many of each form attribute type we have */
                    $types_to_fetch_with_attribute_count = $form_attributes
                        ->groupBy('type')
                        ->map(function ($attrs) {
                            return $attrs->count();
                        });

                    $types_to_fetch = $types_to_fetch_with_attribute_count->keys()->toArray();

                    /* Intersect with requested types */
                    if (count($this->include_value_types) > 0) {
                        $types_to_fetch = array_intersect($types_to_fetch, $this->include_value_types);
                    }

                    /* drop types that we have already fetched
                       BUT only if there is a SINGLE attribute of that type in the form,
                       since the SQL JOINs that we have run previously are only good for fetching
                       a SINGLE value of each type for each post.
                       If we don't do this, we would be leaving out the values of the second
                       and subsequent attributes defined with that type */
                    $already_obtained_types = collect($already_obtained_types)
                        ->filter(function ($type) use ($types_to_fetch_with_attribute_count) {
                            return $types_to_fetch_with_attribute_count->get($type) < 2;
                        })->toArray();
                    $types_to_fetch = array_diff($types_to_fetch, $already_obtained_types);
                }
            }

            if ($types_to_fetch === null || !empty($types_to_fetch)) {
                $other_values = $this->getPostValues(
                    $data['id'],
                    $excludePrivateValues,
                    $excludeStages,
                    $types_to_fetch ?? $this->include_value_types
                );
            }

            //
            $data['values'] = $other_values + $values;

            // If we are not limiting ourselves to the most basic core properites
            if ($this->search_output_type !== 'core') {
                // Continued for legacy
                $data['tags'] = $this->getTagsForPost($data['id'], $data['form_id']);
                $data['sets'] = $this->getSetsForPost($data['id']);
                $data['completed_stages'] = $this->getCompletedStagesForPost(
                    $data['id'],
                    $excludePrivateValues,
                    $excludeStages
                );
                $data['lock'] = null;

                // @todo move or double up in formatter. That should enforce what users can see
                if ($this->postPermissions->canUserSeePostLock($user, new Post($data))) {
                    $data['lock'] = $this->getHydratedLock($data['id']);
                }
            }
        }

        return new Post($data);
    }

    protected function getHydratedLock($post_id)
    {
        $lock_array = $this->post_lock_repo->getNoneExpiredPostLock($post_id);

        return $lock_array ? service("formatter.entity.post.lock")->__invoke(new PostLock($lock_array)) : null;
    }


    // Concerns\JsonTranscode
    protected function getJsonProperties()
    {
        return ['published_to'];
    }

    // Override selectQuery to fetch 'value' from db as text
    protected function selectQuery(array $where = [])
    {
        $query = parent::selectQuery($where);

        // Join to messages and load message id
        $query
            ->join('messages', 'LEFT')
            ->on('posts.id', '=', 'messages.post_id')
            ->select(
                ['messages.id', 'message_id'],
                ['messages.type', 'source']
            );

        /*
         * The above join is optimized by the (post_id,type) index on messages.
         *
         * Add now a separate join into same table, to retrieve more message details.
         *
         * Compared to having all the details come from a single join, this will speed
         * things up *very* considerably, *IF* there are many messages, *BUT* rather
         * few matches of posts to messages.
         * (This easily happens when twitter searches have few usable results,
         *  spam filled e-mail inboxes are being imported, etc)
         */
        $query
            ->join(['messages', 'msgs2'], 'LEFT')
            ->on('msgs2.id', '=', 'messages.id')
            ->select(
                ['msgs2.contact_id', 'contact_id'],
                ['msgs2.data_source_message_id', 'data_source_message_id']
            );

        // Join to form
        $query
            ->join('forms', 'LEFT')
            ->on('posts.form_id', '=', 'forms.id')
            ->select(['forms.color', 'color']);

        return $query;
    }

    // SearchRepository
    public function setSearchParams(Search $search)
    {
        $this->search_query = $this->selectQuery();

        $sorting = $search->getSorting();

        if (!empty($sorting['orderby'])) {
            $order = isset($sorting['order']) ? strtoupper($sorting['order']) : 'ASC';
            $this->search_query->order_by(
                $this->getTable() . '.' . $sorting['orderby'],
                ($order == 'DESC' ? 'DESC' : 'ASC')
            );
        }

        if (!empty($sorting['offset'])) {
            $this->search_query->offset(intval($sorting['offset']));
        }

        if ($search->getFilter('limitPosts')) {
            // the post management permission is associated with bigger limits
            // we would like to do this with API scopes in the future
            $isUnprivileged = !$this->postPermissions->canUserManagePosts($this->getUser());
            $requestedLimit = $sorting['limit'] ?? 0;
            if ($isUnprivileged) {
                $unprivilegedLimit = config('posts.list_max_limit');
                if (!$requestedLimit || $requestedLimit > $unprivilegedLimit) {
                    $sorting['limit'] = $unprivilegedLimit;
                }
            } else {
                $privilegedLimit = config('posts.list_admin_max_limit');
                if (!$requestedLimit || $requestedLimit > $privilegedLimit) {
                    $sorting['limit'] = $privilegedLimit;
                }
            }
        }
        if (array_key_exists('limit', $sorting)
            && $sorting['limit'] >= 0
            && strlen($sorting['limit'])) {
            $this->search_query->limit(intval($sorting['limit']));
        }

        // apply the unique conditions of the search
        $this->setSearchConditions($search);

        // pre-fetch map of form attributes for later use
        $this->fetchFormAttributes($search->form ?? []);

        // optimize results by adding some values coming from other tables right
        // onto the search query
        $this->addSearchResultValues();

        // remember the desired output
        $this->search_output_type = $search->output_core_post ? 'core' : 'full';

        // for use in tests
        return $this->search_query;
    }

    /**
     * Pre-fetch some values right on the search result
     * (instead of doing it in a loop calling getEntity())
     *
     * NOTE: This must not be done on queries that do grouping
     */
    protected function addSearchResultValues()
    {
        // Point columns
        if (!$this->include_value_types || in_array('point', $this->include_value_types)) {
            // Add 'point_value' and 'point_attribute_key' columns with
            // point data type data
            $this->search_query
                ->join('post_point', 'LEFT')
                ->on('posts.id', '=', 'post_point.post_id')
                ->join(['form_attributes', 'point_attribute'], 'LEFT')
                ->on('post_point.form_attribute_id', '=', 'point_attribute.id')
                ->select(
                    [DB::expr('ST_AsText(post_point.value)'), 'point_value'],
                    ['point_attribute.key', 'point_attribute_key']
                );
        }

        // Geometry columns
        if (!$this->include_value_types || in_array('geometry', $this->include_value_types)) {
            // Add 'geometry_value' and 'geometry_attribute_key' columns with
            // point data type data
            $this->search_query
                ->join('post_geometry', 'LEFT')
                ->on('posts.id', '=', 'post_geometry.post_id')
                ->join(['form_attributes', 'geometry_attribute'], 'LEFT')
                ->on('post_geometry.form_attribute_id', '=', 'geometry_attribute.id')
                ->select(
                    [DB::expr('ST_AsText(post_geometry.value)'), 'geometry_value'],
                    ['geometry_attribute.key', 'geometry_attribute_key']
                );
        }
    }

    protected function getPostValues($id, $excludePrivateValues, $excludeStages, $includeTypesOverride = null): array
    {
        if ($includeTypesOverride) {
            $includeTypes = $includeTypesOverride;
        } else {
            $includeTypes = $this->include_value_types;
        }

        // Get all the values for the post. These are the EAV values.
        $values = $this->post_value_factory
            ->proxy($includeTypes)
            ->getAllForPost($id, $this->include_attributes, $excludeStages, $excludePrivateValues);

        $output = [];
        foreach ($values as $value) {
            if (empty($output[$value->key])) {
                $output[$value->key] = [];
            }
            if (is_array($value->value) && isset($value->value['o_filename']) && isset($value->value['id'])) {
                $output[$value->key][] = $value->value['id'];
            } elseif ($value->value !== null) {
                $output[$value->key][] = $value->value;
            }
        }
        return $output;
    }

    protected function getCompletedStagesForPost($id, $excludePrivateValues, $excludeStages)
    {
        $query = DB::select('form_stage_id', 'completed')
            ->from('form_stages_posts')
            ->where('post_id', '=', $id)
            ->where('completed', '=', 1);

        if (!$excludePrivateValues && $excludeStages) {
            $query->where('form_stage_id', 'NOT IN', $excludeStages);
        }

        $result = $query->execute($this->db());

        return $result->as_array(null, 'form_stage_id');
    }

    // OhanzeeRepository
    public function getSearchFields()
    {
        return [
            'status', 'type', 'locale', 'slug', 'user',
            'parent', 'form', 'set', 'q', /* LIKE title, content */
            'created_before', 'created_after',
            'created_before_by_id', 'created_after_by_id',
            'updated_before', 'updated_after',
            'date_before', 'date_after',
            'bbox', 'tags', 'values',
            'center_point', 'within_km',
            'published_to', 'source',
            'post_id', // Search for just a single post id to check if it matches search criteria
            'include_types', 'include_attributes', // Specify values to include
            'include_unmapped',
            'group_by', 'group_by_tags', 'group_by_attribute_key', // Group results
            'timeline', 'timeline_interval', 'timeline_attribute', // Timeline params
            'has_location', //contains a location or not
            'output_core_post',     // only fetch post table data and locations (TBD: tags)
        ];
    }

    // OhanzeeRepository
    protected function setSearchConditions(SearchData $search)
    {
        if ($search->include_types) {
            if (is_array($search->include_types)) {
                $this->include_value_types = $search->include_types;
            } else {
                $this->include_value_types = explode(',', $search->include_types);
            }
        }

        if ($search->include_attributes) {
            if (is_array($search->include_attributes)) {
                $this->include_attributes = $search->include_attributes;
            } else {
                $this->include_attributes = explode(',', $search->include_attributes);
            }
        }

        $query = $this->search_query;
        $table = $this->getTable();

        // Filter by status
        $status = $search->getFilter('status', ['published']);
        //
        if (!is_array($status)) {
            $status = explode(',', $status);
        }
        // If array contains 'all' don't bother filtering
        if (!in_array('all', $status)) {
            $query->where("$table.status", 'IN', $status);
        }
        // End filter by status

        foreach (['type', 'locale', 'slug'] as $key) {
            if ($search->$key) {
                $query->where("$table.$key", '=', $search->$key);
            }
        }

        // If user = me, replace with current user id
        if ($search->user === 'me') {
            $search->user = $this->getUserId();
        }

        foreach (['user', 'parent', 'form'] as $key) {
            if (!empty($search->$key)) {
                // Make sure we have an array
                if (!is_array($search->$key)) {
                    $search->$key = explode(',', $search->$key);
                }

                // Special case: 'none' looks for null
                if (in_array('none', $search->$key)) {
                    $query->and_where_open()
                        ->where("$table.{$key}_id", 'IS', null)
                        ->or_where("$table.{$key}_id", 'IN', $search->$key)
                        ->and_where_close();
                } else {
                    $query->where("$table.{$key}_id", 'IN', $search->$key);
                }
            }
        }

        if ($search->q) {
            // search terms are all wrapped as a series of OR conditions
            $query->and_where_open();

            // searching in title / content
            $query
                ->where("$table.title", 'LIKE', "%$search->q%")
                ->or_where("$table.content", 'LIKE', "%$search->q%");

            if (is_numeric($search->q)) {
                // if `q` is numeric, could be searching for a specific id
                $query->or_where("$table.id", '=', $search->q);
            }

            $query->and_where_close();
        }


        if ($search->id) {
            //searching for specific post id, used for single post in set searches
            $query->where('id', '=', $search->id);
        }

        if ($search->created_before_by_id) {
            $comparison_post = $this->selectOne([
                $this->getTable().'.id' => $search->created_before_by_id
            ]);
            $comparison_post_created = $comparison_post['created'];
            $query->where("$table.created", '<=', $comparison_post_created);
        }

        if ($search->created_after_by_id) {
            $comparison_post = $this->selectOne([
                $this->getTable().'.id' => $search->created_after_by_id
            ]);
            // We're adding 1 second to the time to make sure the result is
            // not inclusive of the query post
            $comparison_post_created = (int)$comparison_post['created'] + 1;
            $query->where("$table.created", '>=', $comparison_post_created);
        }

        // date chcks
        if ($search->created_after) {
            $created_after = strtotime($search->created_after);
            $query->where("$table.created", '>=', $created_after);
        }

        if ($search->created_before) {
            $created_before = strtotime($search->created_before);
            $query->where("$table.created", '<=', $created_before);
        }

        if ($search->updated_after) {
            $updated_after = strtotime($search->updated_after);
            $query->where("$table.updated", '>=', $updated_after);
        }

        if ($search->updated_before) {
            $updated_before = strtotime($search->updated_before);
            $query->where("$table.updated", '<=', $updated_before);
        }

        if ($search->date_after) {
            $date_after = date_create($search->date_after, new \DateTimeZone('UTC'));
            // Convert to UTC (needed in case date came with a tz)
            $date_after->setTimezone(new \DateTimeZone('UTC'));
            $query->where("$table.post_date", '>', $date_after->format('Y-m-d H:i:s'));
        }

        if ($search->date_before) {
            $date_before = date_create($search->date_before, new \DateTimeZone('UTC'));
            // Convert to UTC (needed in case date came with a tz)
            $date_before->setTimezone(new \DateTimeZone('UTC'));
            $query->where("$table.post_date", '<=', $date_before->format('Y-m-d H:i:s'));
        }

        // Bounding box search
        // Create geometry from bbox (or create bbox from center & radius)
        $bounding_box = null;
        if ($search->bbox) {
            $bounding_box = $this->createBoundingBoxFromCSV($search->bbox);
        } elseif ($search->center_point && $search->within_km) {
            $bounding_box = $this->createBoundingBoxFromCenter(
                $search->center_point,
                $search->within_km
            );
        }

        if ($bounding_box) {
            $query
                ->join([
                    $this->getBoundingBoxSubquery($bounding_box), 'Filter_BBox'
                ], 'INNER')
                ->on('posts.id', '=', 'Filter_BBox.post_id')
                ;
        }

        // Published to
        if ($search->published_to) {
            $query
                ->where("$table.published_to", 'LIKE', "%'$search->published_to'%")
                ;
        }

        if ($sources = $search->source) {
            if (!is_array($sources)) {
                $sources = explode(',', $sources);
            }

            // Special case: 'web' looks for null
            if (in_array('web', $sources)) {
                $query->and_where_open()
                    ->where("messages.type", 'IS', null)
                    ->or_where("messages.type", 'IN', $sources)
                    ->and_where_close();
            } else {
                $query->where("messages.type", 'IN', $sources);
            }
        }

        // Post id
        if ($post_id = $search->post_id) {
            if (!is_array($post_id)) {
                $post_id = explode(',', $post_id);
            }

            $query
                ->where("$table.id", 'IN', $post_id)
                ;
        }

        if ($search->has_location === 'mapped') {
            $query->and_where_open()
            ->where(
                "$table.id",
                'IN',
                DB::query(Database::SELECT, 'select post_geometry.post_id from post_geometry')
            )
            ->or_where(
                "$table.id",
                'IN',
                DB::query(Database::SELECT, 'select post_point.post_id from post_point')
            )
            ->and_where_close();
        } elseif ($search->has_location === 'unmapped') {
            $query->where(
                "$table.id",
                'NOT IN',
                DB::query(Database::SELECT, 'select post_geometry.post_id from post_geometry')
            );
            $query->where(
                "$table.id",
                'NOT IN',
                DB::query(Database::SELECT, 'select post_point.post_id from post_point')
            );
        }

        // Filter by tag
        // @todo add filter by specific tag attribute?
        if (!empty($search->tags)) {
            if (isset($search->tags['any'])) {
                $tags = $search->tags['any'];
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }

                $query
                    ->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
                    ->where('tag_id', 'IN', $tags);
            } elseif (isset($search->tags['all'])) {
                $tags = $search->tags['all'];
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }

                foreach ($tags as $tag) {
                    $sub = DB::select('post_id')
                        ->from('posts_tags')
                        ->where('tag_id', '=', $tag);

                    $query
                        ->where('posts.id', 'IN', $sub);
                }
            } else {
                $tags = $search->tags;
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }

                $query
                    ->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
                    ->where('tag_id', 'IN', $tags);
            }
        }

        // Filter by set
        if (!empty($search->set)) {
            $set = $search->set;
            if (!is_array($set)) {
                $set = explode(',', $set);
            }

            $query
                ->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
                ->where('posts_sets.set_id', 'IN', $set);
        }

        // Attributes
        if ($search->values) {
            foreach ($search->values as $key => $value) {
                $attribute = $this->form_attribute_repo->getByKey($key);

                if (!is_array($value)) {
                    $value = explode(',', $value);
                }

                $sub = $this->post_value_factory
                    ->getRepo($attribute->type)
                    ->getValueQuery($attribute->id, $value);

                $query
                    ->join([$sub, 'Filter_'.ucfirst($key)], 'INNER')
                    ->on('posts.id', '=', 'Filter_'.ucfirst($key).'.post_id');
            }
        }

        $user = $this->getUser();
        // If there's no logged in user, or the user isn't admin
        // restrict our search to make sure we still return SOME results
        // they are allowed to see

        if (!$user->id) {
            $query->where("$table.status", '=', 'published');
        } elseif (!$this->postPermissions->canUserViewUnpublishedPosts($user)) {
            $query
                ->and_where_open()
                ->where("$table.status", '=', 'published')
                ->or_where("$table.user_id", '=', $user->id)
                ->and_where_close();
        }
    }

    // SearchRepository
    public function getSearchTotal()
    {
        // Assume we can simply count the results to get a total
        $query = $this->getSearchQuery(true)
            ->resetSelect()
            ->select([DB::expr('COUNT(DISTINCT posts.id)'), 'total']);

        // Fetch the result and...
        $results = $query->execute($this->db());
        // ... return the total.
        $total = 0;

        foreach ($results->as_array() as $result) {
            $total += array_key_exists('total', $result) ? (int) $result['total'] : 0;
        }

        return $total;
    }

    public function getUnmappedTotal($total_posts)
    {

        $mapped = 0;
        $raw_sql = "select count(distinct post_id) as 'total' from (select post_geometry.post_id from post_geometry
            union
            select post_point.post_id from post_point) as sub;";
        if ($total_posts > 0) {
            $results = DB::query(Database::SELECT, $raw_sql)->execute($this->db());

            foreach ($results->as_array() as $result) {
                $mapped = array_key_exists('total', $result) ? (int) $result['total'] : 0;
            }
        }
        return $total_posts - $mapped;
    }

    // PostRepository
    public function getPublishedTotal()
    {
        return (int) $this->selectCount(['posts.status' => 'published']);
    }

    // PostRepository
    public function getTotal()
    {
        return (int) $this->selectCount(['posts.type' => 'report']);
    }

    // StatsPostRepository
    public function getGroupedTotals(SearchData $search)
    {
        // Create a new query to select posts count
        $this->search_query = DB::select([DB::expr('COUNT(DISTINCT posts.id)'), 'total'])
                ->from('posts')
                ->JOIN('messages', 'LEFT')
                ->ON('posts.id', '=', 'messages.post_id');

        // Quick hack to ensure all posts are available to
        // group_by=status
        if ($search->group_by === 'status' && ! $search->status) {
            $search->status = 'all';
        }

        // Set filters
        // Note: we're calling setSearchConditions, not setSearchParams
        // because we don't want to set sorting params
        $this->setSearchConditions($search);

        // Group by time-intervals
        if ($search->timeline) {
            // Default to posts created
            $time_field = 'posts.created';

            if ($search->timeline_attribute === 'created' || $search->timeline_attribute == 'updated') {
                // Assumed created / updated means the builtin posts created/updated times
                $time_field = 'posts.' . $search->timeline_attribute;
            } elseif ($search->timeline_attribute) {
                // Find the attribute
                $key = $search->timeline_attribute;
                $attribute = $this->form_attribute_repo->getByKey($key);
                if ($attribute) {
                    // Get the post_TYPE table.
                    $sub = $this->post_value_factory
                        ->getRepo($attribute->type)
                        ->getValueTable();

                    // Join to attribute
                    $this->search_query
                        ->join([$sub, 'Time_'.ucfirst($key)], 'INNER')
                            ->on('form_attribute_id', '=', DB::expr($attribute->id))
                            ->on('posts.id', '=', 'Time_'.ucfirst($key).'.post_id');

                    // Use the attribute `value` as our time
                    $time_field = 'Time_'.ucfirst($key).'.value';
                }
            }

            $this->search_query
                ->select([
                    DB::expr(
                        'FLOOR('.$time_field.'/:interval)*:interval',
                        [':interval' => (int)$search->getFilter('timeline_interval', 86400)]
                    ),
                    'time_label'
                ])
                ->group_by('time_label');
        }

        // Group by attribute
        if ($search->group_by === 'attribute' and $search->group_by_attribute_key) {
            $key = $search->group_by_attribute_key;
            $attribute = $this->form_attribute_repo->getByKey($key);

            if ($attribute) {
                $sub = $this->post_value_factory
                    ->getRepo($attribute->type)
                    ->getValueTable();

                $this->search_query
                    ->join([$sub, 'Group_'.ucfirst($key)], 'INNER')
                        ->on('form_attribute_id', '=', DB::expr($attribute->id))
                        ->on('posts.id', '=', 'Group_'.ucfirst($key).'.post_id')
                    ->select(['Group_'.ucfirst($key).'.value', 'label'])
                    ->group_by('label');
            }
        // Group by status
        } elseif ($search->group_by === 'status') {
            $this->search_query
                ->select(['posts.status', 'label'])
                ->group_by('label');
        // Group by form
        } elseif ($search->group_by === 'form') {
            $this->search_query
                ->join('forms', 'LEFT')->on('posts.form_id', '=', 'forms.id')
                // Select Datasource
                ->select(['messages.type', 'type'])
                // This should really use ANY_VALUE(forms.name) but that only exists in mysql5.7
                ->select([DB::expr('MAX(forms.name)'), 'label'])
                ->select(['forms.id', 'id'])
                // First group by form...
                ->group_by('forms.id')
                // ...and then by datasource
                ->group_by('messages.type');
        // Group by tags
        } elseif ($search->group_by === 'tags') {
            /**
             * The output query looks something like
             * SELECT
             * `parents`.`tag` AS `label`,
             * COUNT(DISTINCT posts.id) AS `total`
             * FROM `posts`
             * JOIN `posts_tags` ON (`posts`.`id` = `posts_tags`.`post_id`)
             * JOIN `tags` ON (`posts_tags`.`tag_id` = `tags`.`id`)
             * JOIN `tags` as `parents`
             *   ON (`parents`.`id` = `tags`.`parent_id` OR `parents`.`id` = `posts_tags`.`tag_id`)
             * WHERE `status` = 'published' AND `posts`.`type` = 'report'
             * AND `parents`.`parent_id` IS NULL
             * GROUP BY `parents`.`id`
             */

            // Count by tag but also include child counts in the parent count
            $this->search_query
                ->join('posts_tags')->on('posts.id', '=', 'posts_tags.post_id')
                ->join('tags')->on('posts_tags.tag_id', '=', 'tags.id')
                ->join(['tags', 'parents'])
                    // Slight hack to avoid kohana db forcing multiple ON clauses to use AND not OR.
                    ->on(
                        DB::expr("`parents`.`id` = `tags`.`parent_id` OR `parents`.`id` = `posts_tags`.`tag_id`"),
                        '',
                        DB::expr("")
                    )
                // This should really use ANY_VALUE(forms.name) but that only exists in mysql5.7
                ->select([DB::expr('MAX(parents.tag)'), 'label'])
                ->select(['parents.id', 'id'])
                ->group_by('parents.id');

            // Limit tags to a top level, or a specific parent.
            if ($search->group_by_tags !== 'all') {
                if ($search->group_by_tags) {
                    $this->search_query
                        ->where('parents.parent_id', '=', $search->getFilter('group_by_tags', null));
                } else {
                    // Special case: top level categories could have parent_id NULL or 0
                    // @todo try to ensure parent_id is always NULL and migrate 0 -> NULL
                    $this->search_query
                        ->and_where_open()
                        ->where('parents.parent_id', 'IS', null)
                        ->or_where('parents.parent_id', '=', 0)
                        ->and_where_close();
                }
            }
        // If no group_by just count all posts
        } else {
            $this->search_query
                ->select([DB::expr('"all"'), 'label']);
        }

        // .. Add orderby time *after* order by groups
        if ($search->timeline) {
            // Order by label, then time
            $this->search_query->order_by('label');
            $this->search_query->order_by('time_label');
        } else {
            // Order by count, then label
            $this->search_query->order_by('total', 'DESC');
            $this->search_query->order_by('label');
        }

        // Fetch the results and...
        $results = $this->search_query->execute($this->db());
        $results = $results->as_array();
        if ($search->include_unmapped) {
            // Append unmapped totals to stats
            $results['unmapped'] = $this->getUnmappedTotal($this->getSearchTotal());
        }
        // ... return them as an array
        return $results;
    }

    // PostRepository
    public function getByIdAndParent($id, $parent_id, $type)
    {
        return $this->getEntity($this->selectOne([
            'posts.id' => $id,
            'posts.parent_id' => $parent_id,
            'posts.type' => $type
        ]));
    }

    // PostRepository
    public function getByLocale($locale, $parent_id, $type)
    {
        return $this->getEntity($this->selectOne([
            'posts.locale' => $locale,
            'posts.parent_id' => $parent_id,
            'posts.type' => $type
        ]));
    }

    /**
     * Return a Bounding Box given a CSV of west,north,east,south points
     *
     * @param  string $csv 'west,north,east,south'
     * @return BoundingBox
     */
    private function createBoundingBoxFromCSV($csv)
    {
        list($bb_west, $bb_north, $bb_east, $bb_south)
                = array_map('floatval', explode(',', $csv))
                ;

        $bounding_box_factory = $this->bounding_box_factory;
        return $bounding_box_factory($bb_west, $bb_north, $bb_east, $bb_south);
    }

    private function createBoundingBoxFromCenter($center, $within_km = 0)
    {
        // if a $center point and $within_km distance was given,
        // create a bounding box that matches those conditions.
        $center_point = explode(',', $center);
        $center_lat = $center_point[0];
        $center_lon = $center_point[1];

        $bounding_box_factory = $this->bounding_box_factory;
        $bounding_box = $bounding_box_factory(
            $center_lon,
            $center_lat,
            $center_lon,
            $center_lat
        );

        if ($within_km) {
            $bounding_box->expandByKilometers($within_km);
        }

        return $bounding_box;
    }

    /**
     * Get a subquery to return post_point entries within a bounding box
     * @param  string $bbox Bounding box
     * @return Database_Query
     */
    private function getBoundingBoxSubquery(BoundingBox $bounding_box)
    {
        return DB::select('post_id')
            ->from('post_point')
            ->where(
                DB::expr(
                    'CONTAINS(ST_GeomFromText(:bounds), value)',
                    [':bounds' => $bounding_box->toWKT()]
                ),
                '=',
                1
            );
    }

    /**
     * Get tags for a post
     * @param  int   $id  post id
     * @return array      tag ids for post
     */
    private function getTagsForPost($id, $form_id)
    {
        list($attr_id, $attr_key) = $this->getFirstTagAttr($form_id);

        $result = DB::select('tag_id')->from('posts_tags')
            ->where('post_id', '=', $id)
            ->where('form_attribute_id', '=', $attr_id)
            ->execute($this->db());
        return $result->as_array(null, 'tag_id');
    }

  /**
     * Get sets for a post
     * @param  int   $id  post id
     * @return array      set ids for post
     */
    private function getSetsForPost($id)
    {
        $result = DB::select('set_id')->from('posts_sets')
            ->where('post_id', '=', $id)
            ->execute($this->db());
        return $result->as_array(null, 'set_id');
    }

    // UpdatePostRepository
    public function isSlugAvailable($slug)
    {
        return $this->selectCount(compact('slug')) === 0;
    }


    // UpdatePostRepository
    public function doesTranslationExist($locale, $parent_id, $type)
    {
        // If this isn't a translation of an existing post, skip
        if ($type != 'translation') {
            return true;
        }

        // Is locale the same as parent?
        $parent = $this->get($parent_id);
        if ($parent->locale === $locale) {
            return false;
        }

        // Check for other translations
        return $this->selectCount([
            'posts.type' => 'translation',
            'posts.parent_id' => $parent_id,
            'posts.locale' => $locale
            ]) === 0;
    }

    // UpdateRepository
    public function create(Entity $entity)
    {
        $post = $entity->asArray();

        if (!array_key_exists('created', $post) || $post['created'] === null) {
            $post['created'] = time();
        }

        // Remove attribute values and tags
        unset(
            $post['values'],
            $post['tags'],
            $post['completed_stages'],
            $post['sets'],
            $post['source'],
            $post['color'],
            $post['lock']
        );

        // Set default value for post_date
        // @todo move to entity
        if (empty($post['post_date'])) {
            $post['post_date'] = date_create()->format("Y-m-d H:i:s");
        // Convert post_date to mysql format
        } else {
            $post['post_date'] = $post['post_date']->format("Y-m-d H:i:s");
        }

        // Create the post
        $id = $this->executeInsert($this->removeNullValues($post));

        $values = $entity->values;
        // Handle legacy post.tags attribute
        if ($entity->tags) {
            // Find first tag attribute
            list($attr_id, $attr_key) = $this->getFirstTagAttr($entity->form_id);

            // If we don't have tags in the values, use the post.tags value
            if ($attr_key && !isset($values[$attr_key])) {
                $values[$attr_key] = $entity->tags;
            }
        }

        if ($entity->values) {
            // Update post-values
            $this->updatePostValues($id, $values);
        }

        if ($entity->completed_stages) {
            // Update post-stages
            $this->updatePostStages($id, $entity->form_id, $entity->completed_stages);
        }

        // TODO: Revist post-Kohana
        // This might be better placed in the usecase but
        // given Kohana's future I've put it here
        $this->emit($this->event, $id, 'create');

        return $id;
    }

    public function createMany(Collection $collection) : array
    {
        $this->checkAutoIncMode();

        $first = $collection->first()->asArray();
        unset(
            $first['values'],
            $first['tags'],
            $first['completed_stages'],
            $first['sets'],
            $first['source'],
            $first['color'],
            $first['lock'],
            $first['message_id'],
            $first['data_source_message_id'],
            $first['contact_id']
        );
        $columns = array_keys($first);

        $values = $collection->map(function ($entity) {
            $data = $entity->asArray();

            if (!array_key_exists('created', $data) || $data['created'] === null) {
                $data['created'] = time();
            }

            unset(
                $data['values'],
                $data['tags'],
                $data['completed_stages'],
                $data['sets'],
                $data['source'],
                $data['color'],
                $data['lock'],
                $data['message_id'],
                $data['data_source_message_id'],
                $data['contact_id']
            );

            // Set default value for post_date
            // @todo move to entity
            if (empty($data['post_date'])) {
                $data['post_date'] = date_create()->format("Y-m-d H:i:s");
            // Convert post_date to mysql format
            } else {
                $data['post_date'] = $data['post_date']->format("Y-m-d H:i:s");
            }

            // JSON encode values
            $data = $this->json_transcoder->encode(
                $data,
                $this->getJsonProperties()
            );

            return $data;
        })->all();

        $query = DB::insert($this->getTable())
            ->columns($columns);

        call_user_func_array([$query, 'values'], $values);

        list($insertId, $created) = $query->execute($this->db());
        $newPostIds = range($insertId, $insertId + $created - 1);

        // Loop over entities, and aggregate values by attribute
        // Combine post ids with entities
        $postsById = collect($newPostIds)->combine($collection);

        // Grab values from post entities, combined with attribute key and post id
        // Each set of values is still grouped by post id at this point
        $postValues = $postsById->map(function ($entity, $id) {
            return collect($entity->values)->map(function ($value, $key) use ($id) {
                return compact('value', 'key', 'id');
            })->all();
        })
        // Flatten post values to a single level
        ->flatten(1)
        // Group by attribute key
        ->groupBy('key')
        // For each group of post values...
        ->each(function ($values, $key) {
            // Get the form attribute
            $attribute = $this->form_attribute_repo->getByKey($key);
            if (!$attribute->id) {
                return;
            }

            // Handle media items especially, to save the media entities first
            if ($attribute->type === 'media') {
                $values = $this->createManyMedia($values);
            }

            // Get the correct post value repo for the attribute
            $repo = $this->post_value_factory->getRepo($attribute->type);

            // Bulk insert the post values in the post_... table
            if ($values->count() > 0) {
                $repo->createManyValues($values->all(), $attribute->id);
            }
        });

        // Save completed stages
        // We're not bothering to batch insert this step because its not heavily used.
        $postsById->each(function ($entity, $id) {
            if ($entity->completed_stages) {
                $this->updatePostStages($id, $entity->form_id, $entity->completed_stages);
            }
        });

        // NB: We don't handle legacy post.tags during bulk insert

        return $newPostIds;
    }

    public function createManyMedia($values)
    {
        // @todo inject this
        $mediaRepo = service('repository.media');

        // Loop over all media values
        return $values->map(function ($group) use ($mediaRepo) {
            $group['value'] = collect($group['value'])->map(function ($value) use ($mediaRepo) {
                // If the value is an array, assume it's an unsaved media object
                if (is_array($value)) {
                    // Pass it to the media repo to save, and pass the ID back
                    $value = $mediaRepo->create(new Media($value));
                }

                return $value;
            })->all();

            return $group;
        });
    }

    // UpdateRepository
    public function update(Entity $entity)
    {
        $post = $entity->getChanged();
        $post['updated'] = time();

        // Remove attribute values and tags
        unset(
            $post['values'],
            $post['tags'],
            $post['completed_stages'],
            $post['sets'],
            $post['source'],
            $post['color'],
            $post['lock']
        );

        // Convert post_date to mysql format
        if (!empty($post['post_date'])) {
            $post['post_date'] = $post['post_date']->format("Y-m-d H:i:s");
        }

        $count = $this->executeUpdate(['id' => $entity->id], $post);

        $values = $entity->values;

        // Handle legacy post.tags attribute
        if ($entity->hasChanged('tags')) {
            // Find first tag attribute
            list($attr_id, $attr_key) = $this->getFirstTagAttr($entity->form_id);
            // If we don't have tags in the values, use the post.tags value
            if ($attr_key && !isset($values[$attr_key])) {
                $values[$attr_key] = $entity->tags;
            }
        }

        if ($entity->hasChanged('values')) {
            // Update post-values
            $this->post_value_factory->proxy()->deleteAllForPost($entity->id);
            $this->updatePostValues($entity->id, $values);
        }

        if ($entity->hasChanged('completed_stages')) {
            // Update post-stages
            $this->updatePostStages($entity->id, $entity->form_id, $entity->completed_stages);
        }

        // TODO: Revist post-Kohana
        // This might be better placed in the usecase but
        // given Kohana's future I've put it here
        $this->emit($this->event, $entity->id, 'update');

        if ($this->post_lock_repo->isActive($entity->id)) {
            $this->post_lock_repo->releaseLock($entity->id);
        }


        return $count;
    }

    // UpdateRepository
    public function updateFromService(Entity $entity)
    {
        $post = $entity->getChanged();
        $post['updated'] = time();
        // Remove attribute values and tags
        unset(
            $post['values'],
            $post['tags'],
            $post['completed_stages'],
            $post['sets'],
            $post['source'],
            $post['color']
        );
        // Convert post_date to mysql format
        if (!empty($post['post_date'])) {
            $post['post_date'] = $post['post_date']->format("Y-m-d H:i:s");
        }
        $count = $this->executeUpdate(['id' => $entity->id], $post);
        $values = $entity->values;
        // Handle legacy post.tags attribute
        if ($entity->hasChanged('tags')) {
            // Find first tag attribute
            list($attr_id, $attr_key) = $this->getFirstTagAttr($entity->form_id);
            // If we don't have tags in the values, use the post.tags value
            if ($attr_key && !isset($values[$attr_key])) {
                $values[$attr_key] = $entity->tags;
            }
        }
        if ($entity->hasChanged('values') || $entity->hasChanged('tags')) {
            // Update post-values
            $this->updatePostValues($entity->id, $values);
        }
        if ($entity->hasChanged('completed_stages')) {
            // Update post-stages
            $this->updatePostStages($entity->id, $entity->form_id, $entity->completed_stages);
        }
        // TODO: Revist post-Kohana
        // This might be better placed in the usecase but
        // given Kohana's future I've put it here
        $this->emit($this->event, $entity->id, 'update');
        return $count;
    }

    public function delete(Entity $entity)
    {
        parent::delete($entity);
        // TODO: Revist post-Kohana
        // This might be better placed in the usecase but
        // given Kohana's future I've put it here
        //$this->emit($this->event, $entity->id, 'delete');
    }

    protected function updatePostValues($post_id, $attributes)
    {
        foreach ($attributes as $key => $values) {
            $attribute = $this->form_attribute_repo->getByKey($key);
            if (!$attribute->id) {
                continue;
            }

            $repo = $this->post_value_factory->getRepo($attribute->type);

            foreach ($values as $val) {
                $repo->createValue($val, $attribute->id, $post_id);
            }
        }
    }

    public function getFirstTagAttr($form_id)
    {
        $result = DB::select('form_attributes.id', 'form_attributes.key')
            ->from('form_attributes')
            ->join('form_stages', 'INNER')->on('form_stages.id', '=', 'form_attributes.form_stage_id')
            ->where('form_stages.form_id', '=', $form_id)
            ->where('form_attributes.type', '=', 'tags')
            ->order_by('form_attributes.priority', 'ASC')
            ->limit(1)
            ->execute($this->db());

        return [$result->get('id'), $result->get('key')];
    }


    protected function updatePostStages($post_id, $form_id, $completed_stages)
    {
        if (! is_array($completed_stages)) {
            $completed_stages = [];
        }

        // Remove any existing entries
        DB::delete('form_stages_posts')
            ->where('post_id', '=', $post_id)
            ->execute($this->db());

        $insert = DB::insert('form_stages_posts', ['form_stage_id', 'post_id', 'completed']);
        // Get all stages for form
        $form_stages = $this->form_stage_repo->getByForm($form_id);
        foreach ($form_stages as $stage) {
            $insert->values([
                $stage->id,
                $post_id,
                in_array($stage->id, $completed_stages) ? 1 : 0
            ]);
        }
        // Execute the insert
        $insert->execute($this->db());
    }

    // SetPostRepository
    public function getPostInSet($post_id, $set_id)
    {
        $result = $this->selectQuery(['posts.id' => $post_id])
            ->select('posts.*')
            ->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
            ->where('posts_sets.set_id', '=', $set_id)
            ->limit(1)
            ->execute($this->db())
            ->current();

        return $this->getEntity($result);
    }

    // PostRepository
    public function doesPostRequireApproval($formId)
    {
        if ($formId) {
            $form = $this->form_repo->get($formId);
            return $form->require_approval;
        }

        return true;
    }
}
