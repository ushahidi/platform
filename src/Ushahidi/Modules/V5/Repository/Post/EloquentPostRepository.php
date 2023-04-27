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

class EloquentPostRepository implements PostRepository
{
    private $queryBuilder;
    public function __construct(Builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }


    private function setSearchCondition(PostSearchFields $search_fields, $query)
    {
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

        if ($search_fields->locale()) {
            $query->where('posts.locale', '=', $search_fields->locale());
        }

        if ($search_fields->slug()) {
            $query->where('posts.slug', '=', $search_fields->slug());
        }
        if ($search_fields->type()) {
            $query->where('posts.type', '=', $search_fields->type());
        }



        // relation filters
        if (count($search_fields->set())) {
            $query->join("posts_sets", 'posts.id', '=', 'posts_sets.post_id');
            $query->whereIn('posts_sets.set_id', $search_fields->set());
        }



        //set

        // if (!empty($search->set)) {
        //     $set = $search->set;
        //     if (!is_array($set)) {
        //         $set = explode(',', $set);
        //     }

        //     $query
        //         ->join('posts_sets', 'INNER')->on('posts.id', '=', 'posts_sets.post_id')
        //         ->where('posts_sets.set_id', 'IN', $set);
        // }

        // if ($search_fields->type()) {
        //     $query->where('type', '=', $search_fields->type());
        // }

        // if ($search_fields->tags()) {
        //     $query->where('tag', '=', $search_fields->tags());
        // }
        // if ($search_fields->parent()) {
        //     $query->where('parent_id', '=', $search_fields->parent());
        // }
        return $query;
    }


    public function findById(int $id, array $fields = [], array $with = []): Post
    {
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

        $query = Post::take($paging->getLimit())
            ->skip($paging->getSkip())
            ->orderBy($paging->getOrderBy(), $paging->getOrder());

        if (count($fields)) {
            $query->select($fields);
        }
        if (count($with)) {
            $query->with($with);
        }

        $query = $this->setSearchCondition($search_fields, $query);
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
        return $query->count();
    }

    private function getGeoJsonQuery()
    {
        $query = DB::table('posts');
        $query->leftJoin('messages', 'messages.post_id', '=', 'posts.id');
        // get color
         $query->leftJoin('forms', 'posts.form_id', '=', 'forms.id');

        $select_raw = "posts.id as id
            ,Max(posts.title) as title
            ,Max(posts.content) as description";
        $select_raw .= ",Max(IFNULL(messages.type,'web')) as source
            ,Max(messages.data_source_message_id) as 'data_source_message_id'";
        $select_raw .=",Max(forms.color) as 'marker-color'";
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
        $query = $this->getGeoJsonQuery();
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

    private function getMainSearchQuery(PostStatsSearchFields $search)
    {
        $search_query = DB::table('posts');
        $search_query->selectRaw('COUNT(DISTINCT posts.id) as total');

        //? why I need this join
        $search_query->leftJoin('messages', 'messages.post_id', '=', 'posts.id');

        // Set filters
        $search_query = $this->setSearchCondition($search, $search_query);
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
                $search_query->join('posts_tags', 'posts.id', '=', 'posts_tags.post_id');
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
