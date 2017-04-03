<?php defined('SYSPATH') OR die('No direct script access.');
 
/** 
* Ushahidi FormsTags Repo Trait
* Helps Forms and Tags-repository use the same methods
** @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
*/

trait Ushahidi_FormsTagsTrait 
{
    //returning forms for a specific Tag-id
    private function getFormsForTag($id) {
        $result = DB::select('form_id') ->from('forms_tags')
            ->where('tag_id', '=', $id)
            ->execute($this->db);
        return $result->as_array(NULL, 'form_id');
    }
    //returning tags for a specific Form-id
    private function getTagsForForm($id) {
        $result = DB::select('tag_id')
            ->from('forms_tags')
            ->where('form_id', '=', $id)
            ->execute($this->db);
        return $result->as_array(NULL, 'tag_id');
    }
    
    // updating/adding tags to a form
    private function updateFormsTags($form_id, $tags) {
        if(!$tags) {
            DB::delete('forms_tags')
                ->where('form_id', '=', $form_id)
                ->execute($this->db);
        } else {
            if($tags){
                $existing = $this->getTagsForForm($form_id);
                $insert = DB::insert('forms_tags', ['form_id', 'tag_id']);
                $tag_ids = [];
                $new_tags = FALSE;
                foreach($tags as $tag) {
                    if(!in_array($tag, $existing)) {
                        $insert->values([$form_id, $tag]);
                        $new_tags = TRUE;
                    }
                    $tag_ids[] = $tag;
                }
                if($new_tags)
                {
                    $insert->execute($this->db);
                }
                if(!empty($tag_ids)) {
                   DB::delete('forms_tags')
                    ->where('tag_id', 'NOT IN', $tag_ids)
                    ->and_where('form_id', '=', $form_id)
                    ->execute($this->db);
                }
            }
        }
    }
    //updating/adding forms to a tag
    private function updateTagForms($tag_id, $forms) {
        if(empty($forms)) {
            DB::delete('forms_tags')
                ->where('tag_id', '=', $tag_id)
                ->execute($this->db);
        } else {
            $existing = $this->getFormsForTag($tag_id);
            $insert = DB::insert('forms_tags', ['form_id', 'tag_id']);
            $form_ids = [];
            $new_forms = FALSE;
            foreach($forms as $form) {
                if(isset($form['id'])) {
                    $id = $form['id'];
                } else {
                    $id = $form;
                }
                if(!in_array($form, $existing)) {
                    $insert->values([$id, $tag_id]);
                    $new_forms = TRUE;
                }
                $form_ids[] = $id;
            }
            
            if($new_forms)
            {
                $insert->execute($this->db);
            }
            
            if(!empty($form_ids)) {
                DB::delete('forms_tags')
                ->where('form_id', 'NOT IN', $form_ids)
                ->and_where('tag_id', '=', $tag_id)
                ->execute($this->db);
             }
        }
    }
} 