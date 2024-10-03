<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostSearchFields extends SearchFields
{
    /**
     * @var ?string
     */
    protected $query;
    protected $post_id;

    protected $status;
    protected $locale;
    protected $slug;
    protected $form;
    protected $form_condition;
    protected $parent;
    protected $parent_none;
    protected $user;
    protected $user_none;

    protected $type;
    protected $created_before_by_id;
    protected $created_after_by_id;
    protected $created_before;
    protected $created_after;
    protected $updated_before;
    protected $updated_after;
    protected $date_before;
    protected $date_after;


    // relations
    protected $source;
    protected $web_source;
    protected $has_location;
    protected $within_km;

    protected $bbox;
    protected $center_point;
    protected $include_unstructured_posts;


    // before ready
    protected $set;
    protected $tags;
    protected $published_to;
    protected $include_types;
    protected $include_attributes;
    protected $output_core_post;

    private function getParameterAsArray($parameter_value)
    {
        $filter_values = [];
        if ($parameter_value) {
            if (is_array($parameter_value)) {
                $filter_values = $parameter_value;
            } else {
                $filter_values = explode(',', $parameter_value);
            }
        }
        return $filter_values;
    }
    private function checkIfEmpty($value, $default = null)
    {
        if (trim($value) === "") {
            return $default;
        } else {
            return $value;
        }
    }
    public function __construct(Request $request)
    {

        $this->query = $request->query('q');
        $this->post_id = $request->query('post_id');
        if ($request->get('status') == 'all') {
            $this->status = []; // no conditions
        } else {
            $this->status = $this->getParameterAsArray($request->get('status'));
        }
        $this->user_none = false;
        if ($request->get('user') == 'me') {
            if (Auth::id()) {
                $this->user = [Auth::id()];
            } else {
                $this->user = []; // no conditions
            }
        } elseif ($request->get('user') == 'none') {
            $this->user = []; // no conditions
            $this->user_none = true;
        } else {
            $this->user = $this->getParameterAsArray($request->get('user'));
        }

        $this->parent_none = false;
        if ($request->get('parent') == 'none') {
            $this->parent = []; // no conditions
            $this->parent_none = true;
        } else {
            $this->parent = $this->getParameterAsArray($request->get('parent'));
        }

        $this->include_unstructured_posts = $request->has('include_unstructured_posts')
            ? filter_var($request->query('include_unstructured_posts'), FILTER_VALIDATE_BOOLEAN)
            : true; // defaulte include unstructured posts
        $this->form_condition = "all";
        $this->form = []; // no conditions
        if (!$request->has('form')) {
            if (!$this->include_unstructured_posts) {
                $this->form_condition = "not_null";
            }
        } else {
            if ($request->get('form') == 'none') {
                $this->form_condition = "null";
            } else {
                $this->form_condition = "include";
                $this->form = $this->getParameterAsArray($request->get('form'));
            }
        }


        if ($request->get('status') == 'all') {
            $this->status = []; // no conditions
        } else {
            $this->status = $this->getParameterAsArray($request->get('status'));
        }


        $this->locale = $request->query('locale');
        $this->slug = $request->query('slug');
        $this->type = $request->query('type');

        $this->created_before_by_id = $this->checkIfEmpty($request->query('created_before_by_id') ?? 0, 0);
        $this->created_after_by_id = $this->checkIfEmpty($request->query('created_after_by_id') ?? 0, 0);
        $this->created_before = strtotime($request->query('created_before')) ?? null;
        $this->created_after = strtotime($request->query('created_after')) ?? null;
        $this->updated_before = strtotime($request->query('updated_before')) ?? null;
        $this->updated_after = strtotime($request->query('updated_after')) ?? null;
        $this->date_before = $request->query('date_before') ?? null;
        $this->date_after = $request->query('date_after') ?? null;


        $this->source = $this->getParameterAsArray($request->get('source'));
        if (in_array('web', $this->source)) {
            $this->web_source = true;
        }
        $this->has_location = $request->query('has_location');
        $this->within_km = $request->query('within_km');
        $this->center_point = $request->query('center_point');
        $this->bbox = $request->query('bbox');

        $this->set = $this->getParameterAsArray($request->get('set'));
        $this->tags = $this->getParameterAsArray($request->get('tags'));
    }




    public function q(): ?string
    {
        return $this->query;
    }
    public function locale(): ?string
    {
        return $this->locale;
    }
    public function slug(): ?string
    {
        return $this->slug;
    }


    public function postID(): ?string
    {
        return $this->post_id;
    }

    public function createdAfterById(): int
    {
        return $this->created_after_by_id;
    }
    public function createdBeforeById(): int
    {
        return $this->created_before_by_id;
    }
    public function createdBefore(): ?string
    {
        return $this->created_before;
    }
    public function createdAfter(): ?string
    {
        return $this->created_after;
    }

    public function updatedBefore(): ?string
    {
        return $this->updated_before;
    }
    public function updatedAfter(): ?string
    {
        return $this->updated_after;
    }
    public function dateBefore(): ?string
    {
        return $this->date_before;
    }
    public function dateAfter(): ?string
    {
        return $this->date_after;
    }

    public function status(): array
    {
        return $this->status;
    }


    public function form(): array
    {
        return $this->form;
    }

    public function excludeFormIds($excluded_form_ids)
    {
        if (!empty($excluded_form_ids)) {
            if (!empty($this->form)) {
                $this->form  = array_diff($this->form, $excluded_form_ids);
            } elseif ($this->form_condition != "null") {
                $this->form  = $excluded_form_ids;
                $this->form_condition = "exclude";
            }
        }
    }

    public function formCondition(): string
    {
        return $this->form_condition;
    }


    public function user(): array
    {
        return $this->user;
    }

    public function userNone(): bool
    {
        return $this->user_none;
    }


    public function parent(): array
    {
        return $this->parent;
    }

    public function parentNone(): bool
    {
        return $this->parent_none;
    }
    public function set(): array
    {
        return $this->set;
    }

    public function tags()
    {
        return $this->tags;
    }

    public function type()
    {
        return $this->type ?? 'report';
    }

    public function source()
    {
        return $this->source;
    }

    public function webSource()
    {
        return $this->web_source;
    }

    public function hasLocation()
    {
        return $this->has_location;
    }

    public function bbox()
    {
        return $this->bbox;
    }

    public function centerPoint()
    {
        return $this->center_point;
    }
    public function withinKm()
    {
        return $this->within_km;
    }
    public function includeUnstructuredPosts()
    {
        return $this->include_unstructured_posts;
    }
}
