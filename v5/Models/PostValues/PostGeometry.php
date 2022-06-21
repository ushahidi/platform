<?php

namespace v5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PostGeometry extends PostValue
{
    /**
     * The column that hold geometrical data.
     *
     * @var array
     */
    protected $geometry_column = 'value';

    /**
     * Select geometrical attributes as text from database.
     *
     * @var bool
     */
    protected $geometryAsText = true;

    public $table = 'post_geometry';

    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param bool $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        if (!empty($this->geometry_column) && $this->geometryAsText === true) {
            $raw = 'ST_AsText(`' . $this->table . '`.`' . $this->geometry_column . '`) as `' .
                $this->geometry_column . '`';
            return parent::newQuery()->addSelect('post_geometry.*', DB::raw($raw));
        }
        return parent::newQuery();
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    public function getRules()
    {
        $rules = [
        ];
        return array_merge(parent::getRules(), $rules);
    }//end getRules()
}//end class
