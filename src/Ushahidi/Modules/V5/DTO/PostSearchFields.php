<?php

namespace Ushahidi\Modules\V5\DTO;

use Illuminate\Http\Request;

class PostSearchFields
{
    /**
     * @var ?string
     */
    private $query;
    private $post_id;

    private $status;
    private $locale;
    private $slug;

    // before ready
    private $type;
    private $parent;
    private $user;
    private $form;
    private $set;
    private $tags;
    private $source;
    private $created_before_by_id;
    private $created_after_by_id;
    private $created_before;
    private $created_after;
    private $updated_before;
    private $updated_after;
    private $date_before;
    private $date_after;
    private $bbox;
    private $center_point;
    private $published_to;
    private $include_types;
    private $include_attributes;
    private $include_unmapped;
    private $has_location;
    private $output_core_post;
    private $within_km;

    public function __construct(Request $request)
    {
        $this->query = $request->query('q');
        $this->post_id = $request->query('post_id');
        $this->status = [];
        if ($request->query('status')) {
            $this->status = explode(',', $request->get('status'));
        }
        $this->locale = $request->query('locale');
        $this->slug = $request->query('slug');
        $this->set = $request->query('set')?explode(',', $request->get('set')):[];


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
        return $this->type;
    }

    public function parent()
    {
        return $this->parent;
    }
}
