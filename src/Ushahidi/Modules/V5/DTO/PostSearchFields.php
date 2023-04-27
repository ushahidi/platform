<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class PostSearchFields
{
    /**
     * @var ?string
     */
    protected $query;
    protected $post_id;

    protected $status;
    protected $locale;
    protected $slug;

    protected $type;

    // before ready
    protected $parent;
    protected $user;
    protected $form;
    protected $set;
    protected $tags;
    protected $source;
    protected $created_before_by_id;
    protected $created_after_by_id;
    protected $created_before;
    protected $created_after;
    protected $updated_before;
    protected $updated_after;
    protected $date_before;
    protected $date_after;
    protected $bbox;
    protected $center_point;
    protected $published_to;
    protected $include_types;
    protected $include_attributes;
    protected $has_location;
    protected $output_core_post;
    protected $within_km;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->post_id = $request->query('post_id');
        $this->status = ['published']; // default filter vlue
        if ($request->query('status')) {
            $this->status = explode(',', $request->get('status'));
        }
        $this->locale = $request->query('locale');
        $this->slug = $request->query('slug');
        $this->set = $request->query('set')?explode(',', $request->get('set')):[];
        $this->type = $request->query('type');


       // $this->tag = $request->query('tag');
      //  $this->type = $request->query('type');
       // $this->parent = $request->query('parent');
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
    public function status(): array
    {
        return $this->status;
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
        return $this->type??'report';
    }

    public function parent()
    {
        return $this->parent;
    }
}
