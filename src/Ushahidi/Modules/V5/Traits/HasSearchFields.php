<?php

namespace Ushahidi\Modules\V5\Traits;

use Ushahidi\Modules\V5\DTO\SearchFields;

trait HasSearchFields
{
    /**
     * @var SearchFields
     */
    private $search_fields;
    
    public function getSearchFields()
    {
        return $this->search_fields;
    }
    
    public function setSearchFields(SearchFields $search_fields)
    {
        $this->search_fields = $search_fields;
    }
}
