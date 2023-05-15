<?php

namespace Ushahidi\Modules\V5\Repository\Post;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Modules\V5\Models\Post\Post;
use Ushahidi\Modules\V5\DTO\Paging;
use Ushahidi\Modules\V5\DTO\PostSearchFields;
use Ushahidi\Modules\V5\DTO\PostStatsSearchFields;
use DB;
use Ushahidi\Core\Tool\BoundingBox;
use Illuminate\Support\Facades\Auth;

class EloquentPostRepository implements PostRepository
{
    private $queryBuilder;
    private $filter_joined_tables;

    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->filter_joined_tables = [];
    }

    private function setGuestConditions($query)
    {
        $user = Auth::user();
        if (!$user || !$user->id) {
            $query->where('posts.status', '=', 'published');
        }
        return $query;
    }

    private function setSearchCondition(PostSearchFields $search_fields, $query)
    {

        // Remove all previous where conditions
        //  $query->getQuery()->wheres = [];

        // table posts filters
        if ($search_fields->q()) {
            if (is_numeric($search_fields->q())) {
                $query->where('posts.id', '=', $search_fields->q());
            } else {
                $query->whereRaw(
                    '(posts.title like ? OR posts.content like ?)',
                    ["%" . $search_fields->q() . "%", "%" . $search_fields->q() . "%"]
                );
            }
        }

        if ($search_fields->postID()) {
            if (is_numeric($search_fields->postID())) {
                $query->where('posts.id', '=', $search_fields->postID());
            }
        }

        if (count($search_fields->status())) {
            $query->whereIn('posts.status', $search_fields->status());
        }

        if (count($search_fields->form())) {
            $query->whereIn('posts.form_id', $search_fields->form());
        } elseif ($search_fields->formNone()) {
            $query->whereNull('posts.form_id');
        }

        if (count($search_fields->user())) {
            $query->whereIn('posts.user_id', $search_fields->user());
        } elseif ($search_fields->userNone()) {
            $query->whereNull('posts.user_id');
        }


        if (count($search_fields->parent())) {
            $query->whereIn('posts.parent_id', $search_fields->parent());
        } elseif ($search_fields->parentNone()) {
            $query->whereNull('posts.parent_id');
        }

        if ($search_fields->locale()) {
            $query->where('posts.locale', '=', $search_fields->locale());
        }

        if ($search_fields->slug()) {
            $query->where('posts.slug', '=', $search_fields->slug());
        }
        if ($search_fields->type()) {
            $query->where('posts.type', '=', $search_fields->type());
        }

        if ($search_fields->createdAfterById()) {
            $query->where('posts.id', '>', $search_fields->createdAfterById());
        }

        if ($search_fields->createdBeforeById()) {
            $query->where('posts.id', '<', $search_fields->createdBeforeById());
        }

        if ($search_fields->createdBefore()) {
            $query->where('posts.created', '<', $search_fields->createdBefore());
        }

        if ($search_fields->createdAfter()) {
            $query->where('posts.created', '>', $search_fields->createdAfter());
        }

        if ($search_fields->updatedBefore()) {
            $query->where('posts.updated', '<', strtotime($search_fields->updatedBefore()));
        }

        if ($search_fields->updatedAfter()) {
            $query->where('posts.updated', '>', strtotime($search_fields->updatedAfter()));
        }

        if ($search_fields->dateBefore()) {
            $query->where('posts.created', '<', strtotime($search_fields->dateBefore()));
        }

        if ($search_fields->dateAfter()) {
            $query->where('posts.created', '>', strtotime($search_fields->dateAfter()));
        }




        // relation filters
        if (count($search_fields->set())) {
            $this->filter_joined_tables[] = 'posts_sets';
            $query->join("posts_sets", 'posts.id', '=', 'posts_sets.post_id');
            $query->whereIn('posts_sets.set_id', $search_fields->set());
        }

        if (count($search_fields->tags())) {
            if (isset($search_fields->tags()['any'])) {
                $tags = $search_fields->tags()['any'];
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }
                $this->filter_joined_tables[] = 'posts_tags';
                $query->join("posts_tags", 'posts.id', '=', 'posts_tags.post_id');
                $query->whereIn('posts_tags.tag_id', $tags);
            } elseif (isset($search->tags['all'])) {
                $tags = $search_fields->tags()['all'];
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }

                foreach ($tags as $tag) {
                    $query->whereRaw("posts.id in (select post_id from posts_tags where tag_id = " . $tag . ")");
                }
            } else {
                $tags = $search_fields->tags();
                if (!is_array($tags)) {
                    $tags = explode(',', $tags);
                }
                $this->filter_joined_tables[] = 'posts_tags';
                $query->join("posts_tags", 'posts.id', '=', 'posts_tags.post_id');
                $query->whereIn('posts_tags.tag_id', $tags);
            }
        }

        if (count($search_fields->source())) {
            $this->filter_joined_tables[] = 'messages';
            $query->leftJoin("messages", 'posts.id', '=', 'messages.post_id');
            if ($search_fields->webSource()) {
                $query->where(function ($builder) use ($search_fields) {
                    $builder->whereNull('messages.type')
                        ->orWhereIn('messages.type', $search_fields->source());
                });
            } else {
                $query->whereIn('messages.type', $search_fields->source());
            }
        }

        if ($search_fields->hasLocation() === 'mapped') {
            $query->whereRaw("posts.id in (select post_point.post_id from post_point)");
        } elseif ($search_fields->hasLocation() === 'unmapped') {
            $query->whereRaw("posts.id not in (select post_point.post_id from post_point)");
        }

        // bbox
        $bounding_box = null;
        if ($search_fields->bbox()) {
            $bounding_box = $this->createBoundingBoxFromCSV($search_fields->bbox());
        } elseif ($search_fields->centerPoint() && $search_fields->withinKm()) {
            $bounding_box = $this->createBoundingBoxFromCenter(
                $search_fields->centerPoint(),
                $search_fields->withinKm()
            );
        }

        if ($bounding_box) {
            // $query->whereIn('posts.id', $this->getBoundingBoxPostIds($bounding_box));
            $query->whereRaw(
                "posts.id in (select post_id from post_point"
                . " where CONTAINS(ST_GeomFromText('" . $bounding_box->toWKT() . "'), value) = 1)"
            );
        }

        return $query;
    }

    private function createBoundingBoxFromCSV($csv)
    {
        list($bb_west, $bb_north, $bb_east, $bb_south)
            = array_map('floatval', explode(',', $csv));
        return new BoundingBox($bb_west, $bb_north, $bb_east, $bb_south);
    }

    private function createBoundingBoxFromCenter($center, $within_km = 0)
    {
        // if a $center point and $within_km distance was given,
        // create a bounding box that matches those conditions.
        $center_point = explode(',', $center);
        $center_lat = $center_point[0];
        $center_lon = $center_point[1];

        $bounding_box = new BoundingBox(
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

    private function getBoundingBoxPostIds(BoundingBox $bounding_box)
    {
        $query = DB::table('post_point')->select('post_id')->distinct();
        $query->whereRaw('CONTAINS(ST_GeomFromText("' . $bounding_box->toWKT() . '"), value) = 1');
        $post_ids = [];
        $results = $query->get();
        foreach ($results as $result) {
            $post_ids[] = $result->post_id;
        }
        return $post_ids;
    }

    private function addPostsTableNamePrefix($fields)
    {
        $after_update = [];
        foreach ($fields as $field) {
            $after_update[] = 'posts.' . $field;
        }
        return $after_update;
    }

    public function findById(int $id, array $fields = [], array $with = []): Post
    {
        $fields = $this->addPostsTableNamePrefix($fields);
        $query = Post::where('id', '=', $id);
        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }

        $post = $query->first();

        if (!$post instanceof Post) {
            throw new NotFoundException('Post not found', 404);
        }
        return $post;
    }

    public function paginate(
        Paging $paging,
        PostSearchFields $search_fields,
        array $fields = [],
        array $with = []
    ): LengthAwarePaginator {
        $fields = $this->addPostsTableNamePrefix($fields);
        $query = Post::take($paging->getLimit())
            //->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        $query = $this->setSearchCondition($search_fields, $query);
        $query = $this->setGuestConditions($query);

        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }

        return $query->paginate($paging->getLimit());
    }

    public function delete(int $id): void
    {
        $this->findById($id, ['id'])->delete();
    }

    public function getCountOfPosts(PostSearchFields $search_fields): int
    {
        $query = Post::select(['id']);
        $query = $this->setSearchCondition($search_fields, $query);
        $query = $this->setGuestConditions($query);
        return $query->count();
    }

    private function getGeoJsonQuery(PostSearchFields $search_fields = null)
    {
        $query = DB::table('posts');
        if ($search_fields) {
            $query = $this->setSearchCondition($search_fields, $query);
        }
        $query = $this->setGuestConditions($query);
        if (!in_array('messages', $this->filter_joined_tables)) {
            $query->leftJoin('messages', 'messages.post_id', '=', 'posts.id');
        }
        // get color
        $query->leftJoin('forms', 'posts.form_id', '=', 'forms.id');

        $select_raw = "posts.id as id
            ,Max(posts.title) as title
            ,Max(posts.content) as description";
        $select_raw .= ",Max(IFNULL(messages.type,'web')) as source
            ,Max(messages.data_source_message_id) as 'data_source_message_id'";
        $select_raw .= ",Max(forms.color) as 'marker-color'";
        $select_raw .= ",CONCAT( 
            '{\"type\":\"FeatureCollection\",'
            ,'\"features\":[', 
                GROUP_CONCAT( 
                    CONCAT( 
                        '{
                            \"type\":\"Feature\",',
                            '\"geometry\":',
                            ST_AsGeoJSON(post_point.value),
                            ',\"properties\":{} }'
                         )
                     SEPARATOR ',' )
            , ']}' ) 
            AS geojson";
        $query->selectRaw($select_raw);
        $query->join('post_point', 'post_point.post_id', '=', 'posts.id');
        $query->groupBy('posts.id');
        return $query;
    }



    public function getPostsGeoJson(
        Paging $paging,
        PostSearchFields $search_fields
    ) {
        $query = $this->getGeoJsonQuery($search_fields);
        $query->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());
        return $query->paginate($paging->getLimit());
    }

    public function getPostGeoJson(int $post_id)
    {
        $query = $this->getGeoJsonQuery();
        $query->where('posts.id', '=', $post_id);
        $post_geo = $query->first();
        if (is_null($post_geo)) {
            throw new NotFoundException('Post ' . $post_id . ' does not have a location info', 404);
        }
        return collect($post_geo);
    }

    public function getPostsStats(PostSearchFields $search_fields)
    {

        return collect($this->getGroupedTotals($search_fields));
    }

    private function getMainSearchQuery(PostStatsSearchFields $search_fields)
    {
        $search_query = DB::table('posts');
        $search_query->selectRaw('COUNT(DISTINCT posts.id) as total');
        $search_query = $this->setSearchCondition($search_fields, $search_query);
        $query = $this->setGuestConditions($search_query);
        if (!in_array('messages', $this->filter_joined_tables)) {
            $search_query->leftJoin('messages', 'messages.post_id', '=', 'posts.id');
        }
        // Set filters
        return $search_query;
    }



    private function getGroupedTotals(PostStatsSearchFields $search)
    {
        // Create a new query to select posts count
        $search_query = $this->getMainSearchQuery($search);

        // Group by time-intervals
        if ($search->timeline()) {
            $time_field = 'posts.created';
            if ($search->timelineAttribute() === 'created' || $search->timelineAttribute() == 'updated') {
                // Assumed created / updated means the builtin posts created/updated times
                $time_field = 'posts.' . $search->timelineAttribute();
            } elseif ($search->timelineAttribute()) {
                //To do : Find the attribute
                $time_field = 'posts.created';
            }

            $search_query
                ->selectRaw(
                    'FLOOR(' . $time_field . '/' . (int) $search->timelineInterval() . ')'
                    . '*' . (int) $search->timelineInterval()
                    . ' as time_label'
                )
                ->groupBy('time_label');
        }

        switch ($search->groupBy()) {
            // Group by attribute
            case 'attribute':
                break;
            // Group by statsus
            case 'status':
                $search_query->selectRaw('posts.status as label , NULL as id');
                $search_query->groupBy('label');

                break;
            // Group by forms
            case 'form':
                $search_query->leftJoin('forms', 'posts.form_id', '=', 'forms.id');
                $search_query->selectRaw(
                    'MAX(forms.name) as label'
                    . ',forms.id as id'
                );
                $search_query->groupBy('forms.id');

                break;
            // Group by tags
            case 'tags':
                if (!in_array('posts_tags', $this->filter_joined_tables)) {
                    $search_query->join('posts_tags', 'posts.id', '=', 'posts_tags.post_id');
                }
                $search_query->join('tags', 'posts_tags.tag_id', '=', 'tags.id');

                if ($search->groupByParentTags() == 'all') {
                    $search_query->selectRaw('MAX(tags.tag) as label,tags.id as id');

                    $search_query->groupBy('tags.id');
                } else {
                    $search_query->Join('tags as parents', function ($join) {
                        $join->on('tags.parent_id', '=', 'parents.id')
                            ->orOn('posts_tags.tag_id', '=', 'parents.id');
                    });

                    $search_query->selectRaw('MAX(parents.tag) as label,parents.id as id');
                    $search_query->groupBy('parents.id');

                    if ($search->groupByParentTags()) {
                        // To do : do we need to return the category it self without childs ?
                        $search_query
                            ->where('parents.parent_id', '=', (int) $search->groupByTags());
                        //->where('parents.parent_id', '=', $search->getFilter('group_by_tags', null));
                    } else {
                        // Special case: top level categories could have parent_id NULL or 0
                        // @todo try to ensure parent_id is always NULL and migrate 0 -> NULL
                        $search_query->whereNull('parents.parent_id');
                        //$search_query->whereOr('parents.parent_id',0);
                    }
                }
                break;
            // no group by
            default:
                $search_query->selectRaw(" Max('all') as label,COUNT(DISTINCT posts.id) as total");

                break;
        }
        if ($search->enableGroupBySource()) {
            $search_query->selectRaw('IFNULL(messages.type,"web") as source');
            $search_query->groupBy('messages.type');
        } else {
            $search_query->selectRaw('MAX("all") as source');
        }

        // order by
        // .. Add orderby time *after* order by groups
        if ($search->timeline()) {
            // Order by label, then time
            $search_query->orderBy('label');
            $search_query->orderBy('time_label');
        } elseif ($search->enableGroupBySource()) {
            // Order by lable then type
            $search_query->orderBy('label');
            $search_query->orderBy('source');
            // $search_query->orderBy('total', 'DESC');
        } else {
            // Order by count, then label
            $search_query->orderBy('total', 'DESC');
            $search_query->orderBy('label');
        }
        $results['group_by_total_posts'] = $search_query->get()->toArray();
        $results['group_by_meta']['group_by'] = $search->groupBy();
        if ($search->groupBy() == 'tags') {
            $results['group_by_meta']['groupByParentTags'] = $search->groupByParentTags();
        } elseif ($search->groupBy() == 'attribute') {
            $results['group_by_meta']['group_by_attribute_key'] = $search->groupByAttributeKey();
        }

        if ($search->timeline()) {
            $results['group_by_meta']['timeline_attribute'] = $search->timelineAttribute();
            $results['group_by_meta']['timeline_interval'] = $search->timelineInterval();
        }


        $results['total_posts'] = $this->getSearchTotal($search);

        if ($search->includeUnmapped()) {
            // Append unmapped totals to stats
            $results['unmapped'] = $this->getUnmappedTotal($search, $results['total_posts']);
        }
        return $results;
    }


    private function getUnmappedTotal(PostStatsSearchFields $search, int $total_posts)
    {
        // we ignored the values in post_geometry

        $mapped = 0;
        if ($total_posts > 0) {
            $search_query = $this->getMainSearchQuery($search);
            $search_query->rightJoin('post_point', 'post_point.post_id', 'posts.id');
            $mapped = $search_query->first()->total;
        }
        return $total_posts - $mapped;
    }

    private function getSearchTotal(PostStatsSearchFields $search)
    {
        return $this->getMainSearchQuery($search)->first()->total;
    }
}
