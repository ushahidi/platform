<?php

namespace v4\Models\PostValues;

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
            $raw =
                'AsText(`' . $this->table . '`.`' . $this->geometry_column . '`) as `' . $this->geometry_column . '`';
            return parent::newQuery()->addSelect('*', DB::raw($raw));
        }
        return parent::newQuery();
    }
    public function getValueAttribute($value)
    {

        $map_config = service('map.config');
        try {
            $geometry = WKT::geomFromText($value);
            if ($geometry instanceof Point) {
                $value = ['lon' => $geometry->lon, 'lat' => $geometry->lat];
//                @TODO if ($this->hideLocation) {
                    // Round to nearest 0.01 or roughly 500m
                    $data['value']['lat'] = round($value['lat'], $map_config['location_precision']);
                    $data['value']['lon'] = round($value['lon'], $map_config['location_precision']);
//                }
            }
        } catch (InvalidText $e) {
            $value = ['lon' => null, 'lat' => null];
        }
        return $value;
    }
    /**
     * Scope helper to only pull tags we are allowed to get from the db
     * @param $query
     * @return mixed
     */
    public function scopeAllowed($query)
    {
        return $query;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
        ];
    }//end validationMessages()

    /**
     * Return all validation rules
     *
     * @return array
     */
    protected function getRules()
    {
        $rules = [
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
        return [parent::getRules(), $rules];
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
