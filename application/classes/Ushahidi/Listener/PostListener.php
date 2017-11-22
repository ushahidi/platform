<?php defined('SYSPATH') or die('No direct script access');

/**
 * Ushahidi PostSet Listener
 *
 * Listens for new posts that are added to a set
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use League\Event\AbstractListener;
use League\Event\EventInterface;
use Ushahidi\Core\Entity\WebhookJobRepository;
use Ushahidi\Core\Entity\WebhookRepository;
use Ushahidi\Core\Entity\PostsChangeLogRepository;
use Ushahidi\Core\Entity\FormStageRepository;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\TagRepository;
use Ushahidi\Core\Traits\RecursiveImplode;

class Ushahidi_Listener_PostListener extends AbstractListener
{
    protected $repo;
    protected $webhook_repo;
    protected $changelog_repo;
	protected $formstages_repo;

    use RecursiveImplode;

    public function setRepo(WebhookJobRepository $repo)
    {
        $this->repo = $repo;
    }

    public function setWebhookRepo(WebhookRepository $webhook_repo)
    {
        $this->webhook_repo = $webhook_repo;
    }

    public function setChangelogRepo(PostsChangeLogRepository $changelog_repo)
    {
        $this->changelog_repo = $changelog_repo;
    }

	public function setFormStagesRepo(FormStageRepository $formstages_repo)
    {
        $this->formstages_repo = $formstages_repo;
    }

    public function setFormAttributesRepo(FormAttributeRepository $form_attrs_repo)
    {
        $this->form_attrs_repo = $form_attrs_repo;
    }

	public function setTagsRepo(TagRepository $tags_repo)
    {
        $this->tags_repo = $tags_repo;
    }


    //TODO: note that we're only receiving the ID here.
    public function handle(EventInterface $event, $postEntity = null, $event_type = null)
    {
        if ($event_type == 'update') {

            $changes_array = $postEntity->getChanged();
            $flat_changeset = [];
            $flat_changeset = $this->getIsolatedChangesForPost($postEntity, $changes_array);

            if (count($flat_changeset) > 0) {
                //TODO: RECONSIDER: currently concatenating all the changes into one long string
                // NOTE: this will be *impossible* to translate.
                // Maybe do a translation code for each server-side field name, then
                //	let the client group them?
                $human_friendly_log = '- Updated fields.<br/>';
                foreach ($flat_changeset as $new_item => $new_value) {
                    //TODO: are these values already sanitized for HTML display?
                    $human_friendly_log .= '- Changed '.str_replace("_", "-", $new_item).' to "'.print_r($new_value, true).'"</br>';
                }
                Kohana::$log->add(Log::DEBUG, 'Human-readable log: '.print_r($human_friendly_log, true).'!');
                $changelog_state = [
                        'post_id' => $postEntity->id,
                        'item_changed' => '',
                        'content' => $human_friendly_log,
                        'entry_type' => 'a',
                    ];
                $changelog_entity = $this->changelog_repo->getEntity();
                $changelog_entity->setState($changelog_state);
                $this->changelog_repo->create($changelog_entity);
            }

        } elseif ($event_type == 'create') {

            //send event info off to webhook
            $state = [
                        'post_id' => $postEntity->id,
                        'event_type' => $event_type
                        ];

            $entity = $this->repo->getEntity();
            $entity->setState($state);
            $this->repo->create($entity);
        } else {
            Kohana::$log->add(Log::DEBUG, 'Unknown event was passed to PostListener ['.print_r($event_type, true).'] ');
        }

    }

    protected function getObjectForTagId($id)
    {
        $tag_entity = $this->tags_repo->get($id);
        return $tag_entity;
    }

    //TODO: implement this
    protected function getFormAttributeObjByKey($key)
    {
        $form_attr_obj = $this->form_attrs_repo->getByKey($key);
        return $form_attr_obj;
    }


	protected function getLabelForCompletedStageId($id)
    {
        $formstage_label = "";
        $formstage_entity = $this->formstages_repo->get($id);
        if(is_object($formstage_entity))
        {
            $formstage_label = $formstage_entity->label;
        }
        return $formstage_label;
    }

    //TODO:  now that we've got a different format for $changed, this need to be updated to handle new/old values
        // such that we don't have to lookup so many values to figure out what changed
    protected function getIsolatedChangesForPost($postEntity, $changed_items)
    {
        $flat_changeset = [];
        $ignored_fields = ['slug', 'updated', 'lock'];
        try {
            foreach ($changed_items as $changed_key => $changed_value) {

                if (is_array($changed_value)) {
                    if ($changed_key == 'values') {

                        //dig into this array,  then just concat the changes as text, since we're
                        // presumably already at the individual field level
                        foreach ($changed_value as $values_key => $values_val)
                        {
                            //TODO:
                            //  first lookup the type of attribute, THEN lookup its label...
                            //  for instance, tags1 is an attribute of category type
                            Kohana::$log->add(Log::INFO, 'Trying to get attribute obj for key: '.print_r($values_key, true));

                            $form_attr_obj = null;

                            $form_attr_obj = $this->getFormAttributeObjByKey($values_key);
                            Kohana::$log->add(Log::INFO, 'Attribute changed: '.print_r($form_attr_obj, true));


                            if ($form_attr_obj !== null && $form_attr_obj->type == 'tags')
                            {
                                    $tag_object = $this->getObjectForTagId($values_val);
                                    if (is_object($tag_object) && $tag_object->type == 'category')
                                    {
                                        Kohana::$log->add(Log::INFO, 'Category updated: '.print_r($tag_object->tag, true));
                                        $flat_changeset = array_merge($flat_changeset, ['category: '.$tag_object->tag  => 'added']);
                                    }
                            }else if ($form_attr_obj !== null){ // we don't know what this is, so we won't look it up

                                $imploded_str = $this->recursiveArrayImplode(" ", $values_val);
                                $flat_changeset = array_merge($flat_changeset, [$form_attr_obj->label => $imploded_str ]);
                            }
                        }

                    } elseif ($changed_key == 'completed_stages')
                    {
                        // TODO: how should we mark stages as incomplete?
                        Kohana::$log->add(Log::INFO, '\n\nUPDATED TASKS/STAGES: '.print_r($changed_items[$changed_key], true));
                        if(is_array($changed_items[$changed_key]))
                        {
                            foreach($changed_items[$changed_key] as $stagekey => $stageval)
                            {
                                $stage_label = $this->getLabelForCompletedStageId($stageval);
                                Kohana::$log->add(Log::ERROR, 'Task completed: '.print_r($stage_label, true));
                                $flat_changeset = array_merge($flat_changeset, ['Task: '.$stage_label  => 'complete']);
                            }
                        }

                    } elseif ($changed_key == 'tags'){
                            Kohana::$log->add(Log::INFO, 'Intercepted a change to tags.');

                    } elseif ($changed_key == 'sets') {
                        //TODO: implement this
                        Kohana::$log->add(Log::INFO, 'Sets were updated');
                    }

                    } else { // not an array
                        if (!in_array($changed_key, $ignored_fields)) {
                            $flat_changeset = array_merge($flat_changeset, [$changed_key => $changed_value ]);
                        }
                    }
                }

        } catch (Exception $e) {
            Kohana::$log->add(Log::ERROR, 'Error trying to log a change: '.print_r($e, true));
        }

        Kohana::$log->add(Log::INFO, 'Here is the full changeset: '.print_r($flat_changeset, true));
        return $flat_changeset;
    }

}
