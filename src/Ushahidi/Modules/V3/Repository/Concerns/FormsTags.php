<?php

/**
* Ushahidi FormsTags Repo Trait
* Helps Forms and Tags-repository use the same methods
** @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

namespace Ushahidi\Modules\V3\Repository\Concerns;

use Ohanzee\DB;

trait FormsTags
{
    //returning tags for a specific Form-id
    private function getTagsForForm($id)
    {
        $attributes = DB::select('form_attributes.options')
            ->from('form_attributes')
            ->join('form_stages')->on('form_stage_id', '=', 'form_stages.id')
            ->join('forms')->on('form_id', '=', 'forms.id')
            ->where('form_id', '=', $id)
            ->where('form_attributes.type', '=', 'tags')
            ->execute($this->db())
            ->as_array();

        $tags = [];
        // Combine all tag ids into 1 array
        foreach ($attributes as $attr) {
            $options = json_decode($attr['options'], true);
            if (is_array($options)) {
                $tags = array_merge($tags, $options);
            }
        }

        return $tags;
    }

    private function removeTagFromAttributeOptions($id)
    {
        // Grab all tags attributes
        $attr = DB::select('id', 'options')
            ->from('form_attributes')
            ->where('type', '=', 'tags')
            ->execute($this->db())
            ->as_array('id', 'options');

        foreach ($attr as $attr_id => $options) {
            $options = json_decode($options, true);
            if (is_array($options) && in_array($id, $options)) {
                // Remove $id from options array
                $index = array_search($id, $options);
                array_splice($options, $index, 1);
                $options = json_encode($options);

                // Save it
                DB::update('form_attributes')
                    ->set(['options' => $options])
                    ->where('id', '=', $attr_id)
                    ->execute($this->db());
            }
        }
    }
}
