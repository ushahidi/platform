<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class PostStatsSearchFields extends PostSearchFields
{
    private $group_by;
    private $enable_group_by_source;
    private $group_by_parent_tag;
    private $group_by_attribute_key;
    private $timeline;
    private $timeline_attribute;
    private $timeline_interval;
    private $include_unmapped;
    protected $form;
    protected $form_condition;
    public $include_unstructured_posts;

    //  getFilter ??


    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->group_by = $request->query('group_by');
        $this->enable_group_by_source = $request->query('enable_group_by_source');
        $this->group_by_parent_tag = $request->query('group_by_parent_tag');
        $this->group_by_attribute_key = $request->query('group_by_attribute_key');
        $this->timeline = $request->query('timeline');
        $this->timeline_attribute = $request->query('timeline_attribute');
        $this->timeline_interval = $request->query('timeline_interval');
        $this->include_unmapped = $request->query('include_unmapped');
        $this->include_unstructured_posts = $request->query('include_unstructured_posts');
        $this->form_condition = "all";
        $this->form = []; // no conditions
        if (!$request->has('form')) {
            if ($request->has('include_unstructured_posts') && !$request->get('include_unstructured_posts')) {
                $this->form_condition = "not_null";
                $this->form = []; // no conditions
            }
        } else {
            if ($request->get('form') == 'none') {
                $this->form = []; // no conditions
                $this->form_condition = "null";
            } else {
                $this->form_condition = "include";
                $this->form = $this->getParameterAsArray($request->get('form'));
            }
        }
    }

    public function groupBy(): ?string
    {
        return $this->group_by;
    }
    public function enableGroupBySource()
    {
        return $this->enable_group_by_source;
    }
    public function groupByParentTags()
    {
        if ($this->group_by_parent_tag == null || $this->group_by_parent_tag == 'all') {
            return 'all';
        }
        if ($this->group_by_parent_tag == "null") {
            return null;
        }
        return $this->group_by_parent_tag;
    }

    public function groupByAttributeKey()
    {
        return $this->group_by_attribute_key;
    }

    public function timeline(): ?string
    {
        return $this->timeline;
    }
    public function timelineAttribute(): ?string
    {
        return $this->timeline_attribute;
    }
    public function timelineInterval()
    {
        return $this->timeline_interval??86400;
    }
    public function includeUnmapped()
    {
        return $this->include_unmapped;
    }
    

    public function status(): array
    {
        if ($this->groupBy() === 'status' && !$this->status) {
            return ['archived','draft','published'];
        }
        return parent::status();
    }

    public function includeUnstructuredPosts()
    {
        return $this->include_unstructured_posts;
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
}
