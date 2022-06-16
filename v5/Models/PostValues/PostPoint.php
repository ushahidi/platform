<?php

namespace v5\Models\PostValues;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symm\Gisconverter\Decoders\WKT;
use Symm\Gisconverter\Exceptions\InvalidText;
use Symm\Gisconverter\Geometry\Point;

class PostPoint extends PostValue
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


    public $table = 'post_point';
    protected $hideLocation = false;

    /**
     * Get a new query builder for the model's table.
     * Manipulate in case we need to convert geometrical fields to text.
     *
     * @param  bool  $excludeDeleted
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        if (!empty($this->geometry_column) && $this->geometryAsText === true) {
            $raw = 'ST_AsText(`' . $this->table . '`.`' . $this->geometry_column . '`) as `' .
                $this->geometry_column . '`';
            return parent::newQuery()->addSelect('post_point.*', DB::raw($raw));
        }
        return parent::newQuery();
    }

    public function hideLocation($hide = true)
    {
        $this->hideLocation = $hide;
    }
    public function getValueAttribute($value)
    {
        $map_config = service('map.config');
        $authorizer = service('authorizer.post');
        $user = $authorizer->getUser();

        $postPermissions = new \Ushahidi\Core\Tool\Permissions\PostPermissions();
        $postPermissions->setAcl($authorizer->acl);
        /**
         * if the user cannot read private values then they also can't see hide_time
         */
        $excludePrivateValues = !$postPermissions->canUserReadPrivateValues(
            $user
        );
        $hide_location = true;
        if ($this->post->survey && !$this->post->survey->hide_location) {
            $hide_location = false;
        }

        if (!$excludePrivateValues) {
            $hide_location = false;
        }
        try {
            $geometry = WKT::geomFromText($value);
            if ($geometry instanceof Point) {
                $value = ['lon' => $geometry->lon, 'lat' => $geometry->lat];
                if ($hide_location) {
                    // Round to nearest 0.01 or roughly 500m
                    $value['lat'] = round($value['lat'], $map_config['location_precision']);
                    $value['lon'] = round($value['lon'], $map_config['location_precision']);
                }
            }
        } catch (InvalidText $e) {
            $value = ['lon' => null, 'lat' => null];
        }
        return $value;
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
            'post_id' => 'nullable|sometimes|exists:posts,id',
            'form_attribute_id' => 'nullable|sometimes|exists:form_attributes,id',
            'value' => [
                'array',
                function ($attribute, $value, $fail) {
                    if (!(isset($value['lat'])) || !(isset($value['lon']))) {
                        return $fail(trans('validation.post_values.point.format'));
                    }
                    if (!($this->checkLat($value['lat']))) {
                        return $fail(trans('validation.post_values.point.lat'));
                    }

                    if (!($this->checkLon($value['lon']))) {
                        return $fail(trans('validation.post_values.point.lon'));
                    }
                }
            ],
        ];
        return $rules;
    }//end getRules()

    private function checkLon($lon)
    {
        if (!is_numeric($lon)) {
            return false;
        }

        if ($lon < -180 || $lon > 180) {
            return false;
        }

        return true;
    }

    private function checkLat($lat)
    {
        if (!is_numeric($lat)) {
            return false;
        }

        if ($lat < -90 || $lat > 90) {
            return false;
        }

        return true;
    }
}//end class
