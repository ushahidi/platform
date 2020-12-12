<?php
/**
 * *
 *  * Ushahidi Generate Entity Translations JSON files
 *  *
 *  * @author     Ushahidi Team <team@ushahidi.com>
 *  * @package    Ushahidi\Application
 *  * @copyright  2020 Ushahidi
 *  * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 *
 *
 */

namespace v5\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Util\MimeType;
use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Ushahidi\Core\Tool\FileData;
use v5\Models\Attribute;
use v5\Models\Category;
use v5\Models\Post;
use v5\Models\Stage;
use v5\Models\Survey;

class GenerateEntityTranslationsJson extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'entitytranslations:out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a JSON file per language with all entity source texts.';
    protected $signature = 'entitytranslations:out';
    protected $batchStamp;
    protected $addPrivateResponses = false;
    protected $addUnpublishedPosts = false;


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->confirm("[Warning] This is an ALPHA Cli feature. Do you want to continue?")) {
            $this->info("Export process cancelled");
            return;
        }
        $warn = "[Data warning] Should we add private responses? Private responses may contain sensitive data.";
        if ($this->confirm($warn)) {
            $this->addPrivateResponses = true;
        }
        $warn = "[Data warning] Should we add non-public posts? This includes in-review and archived posts.";
        if ($this->confirm($warn)) {
            $this->addUnpublishedPosts = true;
        }

        $this->batchStamp = Carbon::now()->format('Ymdhms');
        $this->makeSurveyEntities();
        $this->generatePostEntities();
        $this->generateCategoryEntities();
    }

    /**
     * @return mixed
     */
    private function getCategories()
    {
        return
            Category::all(
                array_merge(['id', 'base_language'], Category::translatableAttributes())
            )
                ->makeHidden(['role', 'parent', 'translations'])
                ->groupBy('base_language');
    }

    /**
     * Translatable items are the basic structure of each row to translate.
     * For each object's translatable attribute, we get its structure to use when creating the JSON output.
     * @param $item
     * @param $output_type
     * @param $context
     * @param $translatable_field
     * @return array|void
     */
    private function makeTranslatableItem($item, $output_type, $context, $translatable_field)
    {
        $toTranslate = $item->$translatable_field;
        if ($toTranslate === null || $toTranslate === "") {
            return false;
        }
        if (is_array($toTranslate) && count($toTranslate) === 0) {
            return false;
        }
        if (is_array($toTranslate) || is_object($toTranslate)) {
            $toTranslate = json_encode($toTranslate);
        }
        return [
            // the item id, to be used when importing
            "id" => $item->id,
            // the language we are translating from
            "base_language" => $item->base_language,
            // the field name we want to translate
            "attribute_name" => $translatable_field,
            // the content of the field we want to translate
            "to_translate" => $toTranslate,
            // this field remains empty since it's what the translator will use to create a translation
            "translation" => "",
            // output_type is used when importing to know what we need to save
            "output_type"=> $output_type,
            // context is just there to help translators understand more of what they are doing
            "context"   => $context
        ];
    }

    /**
     * Creates JSON file with the entities relating to Categories, to be used by translators in their systems
     */
    protected function generateCategoryEntities()
    {
        echo OutputText::info("Gathering translatable Category entities.");
        $attributes = Collection::make(Category::translatableAttributes());
        $categoriesByLang = $this->getCategories();
        $categoriesByLang->each(function ($categories, $language) use ($attributes) {
            $items = Collection::make([]);
            $categories->each(function ($category) use ($attributes, $language, &$items) {
                $attributes->each(function ($tr) use ($category, &$items) {
                    $toSave = $this->makeTranslatableItem($category, 'category', "Category", $tr);
                    if ($toSave) {
                        $items->push($toSave);
                    }
                });
            });
            /**
             * Generates a file per each language containing
             * all the survey related entities base text
             */
            $this->generateFile($items->toJson(), $language, 'categories');
            echo OutputText::info("Created file for categories - based on language: $language.");
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Collection|Post[]
     */
    private function getPosts()
    {
        $posts = null;

        $posts =  Post::query();
        if (!$this->addUnpublishedPosts) {
            $posts = $posts->where('status', '=', 'published');
        }
        $posts = $posts->get(
            array_merge(['id', 'base_language', 'status'], Post::translatableAttributes())
        );
        return $posts
            ->makeHidden(['values', 'translations'])
            ->map(function ($post) {
                $values = $post->getTranslatablePostValues($this->addPrivateResponses)
                    ->map(function ($value) use ($post) {
                        return $this->attachProperties($value, [
                            'output_type' => 'post_value',
                            'post_id' => $post->id,
                            'attribute_name' => $value->attribute->label
                        ])
                                ->makeHidden(['post', 'translations' , 'attribute']);
                    });
                return $this->attachProperties($post, [
                    'output_type' => 'post',
                    'fieldValues' => $values
                ]);
            })->groupBy('base_language');
    }

    /**
     * Creates JSON file with the entities relating to Posts, to be used by translators in their systems
     */
    protected function generatePostEntities()
    {
        echo OutputText::info("Gathering translatable Post entities.");
        $attributes = Collection::make(Post::translatableAttributes());
        $postsByLang = $this->getPosts();
        if (!$postsByLang) {
            return;
        }
        $postsByLang->each(function ($posts, $language) use ($attributes) {
            if (!$language) {
                return;
            }
            $items = Collection::make([]);
            $posts->each(function ($post) use ($attributes, $language, &$items) {
                $attributes->each(function ($tr) use ($post, &$items, $language) {
                    $toSave = $this->makeTranslatableItem($post, 'post', "Post $tr", $tr);
                    if ($toSave) {
                        $items->push($toSave);
                    }
                    $post->fieldValues->flatten()->each(function ($fieldValue) use (&$items, $language, $post) {
                        $fieldValue->base_language = $language;
                        $toSave = $this->makeTranslatableItem(
                            $fieldValue,
                            'post_value_' . $fieldValue->attribute->type,
                            "Field '{$fieldValue->attribute->label}' in post {$post->id}",
                            "value"
                        );
                        if ($toSave) {
                            $items->push($toSave);
                        }
                    });
                });
            });
            /**
             * Generates a file per each language containing
             * all the post related entities base text
             */
            $this->generateFile($items->toJson(), $language, 'posts');
            echo OutputText::info("Created file for posts - based on language: $language.");
        });
    }
    /**
     * @return Collection
     */
    private function getSurveys()
    {

        $surveys = Survey::all(
            array_merge(['id', 'base_language'], Survey::translatableAttributes())
        )->makeHidden(['can_create', 'tasks']);
        $surveys = $this->attachProperties($surveys, ['output_type' => 'survey'])
            ->groupBy('base_language');
        return $surveys;
    }

    /**
     * Creates JSON file with the entities relating to Surveys, to be used by translators in their systems
     */
    protected function makeSurveyEntities()
    {
        echo OutputText::info("Gathering translatable Surveys entities.");
        $surveyAttributes = Collection::make(Survey::translatableAttributes());

        $surveysByLang = $this->getSurveys();
        $surveysByLang->each(function ($surveys, $language) use ($surveyAttributes) {
            if (!$language) {
                return;
            }
            $items = Collection::make([]);
            $surveys->each(function ($survey) use ($surveyAttributes, $language, &$items) {
                $surveyAttributes->each(function ($sAttr) use ($survey, &$items, $language) {
                    $toSave = $this->makeTranslatableItem($survey, 'survey', "Survey", $sAttr);
                    if ($toSave) {
                        $items->push($toSave);
                    }
                    $stageAttributes = Collection::make(Stage::translatableAttributes());
                    $survey->tasks->each(function ($task) use (&$items, $language, $stageAttributes) {
                        $task->base_language = $language;
                        $stageAttributes->each(function ($stgAttr) use (&$items, $task) {
                            $toSave = $this->makeTranslatableItem(
                                $task,
                                'task',
                                "Task in survey $task->form_id",
                                $stgAttr
                            );
                            if ($toSave) {
                                $items->push($toSave);
                            }
                        });
                        $attrAttributes = Collection::make(Attribute::translatableAttributes());
                        $task->fields->each(function ($attribute) use (&$items, $task, $language, $attrAttributes) {
                            $attribute->base_language = $language;
                            $attrAttributes->each(function ($attrAttr) use ($attribute, &$items, $task) {
                                if ($attribute->type === 'tags') {
                                    return;
                                }
                                $toSave = $this->makeTranslatableItem(
                                    $attribute,
                                    'field',
                                    "Field in task $task->id, in survey $task->form_id",
                                    $attrAttr
                                );
                                if ($toSave) {
                                    $items->push($toSave);
                                }
                            });
                        });
                    });
                });
            });
            OutputText::info("Count" . $items->count());
            /**
             * Generates a file per each language containing
             * all the post related entities base text
             */
            $this->generateFile($items->toJson(), $language, 'surveys');
            echo OutputText::info("Created file for surveys - based on language: $language.");
        });
    }
    /**
     * @param $entities
     * @param $properties
     * @return Collection
     */
    private function attachProperties($entities, $properties)
    {
        if ($entities instanceof Collection) {
            return $entities->map(function ($entity) use ($properties) {
                return $this->attachProperties($entity, $properties);
            });
        } else {
            $entity = $entities;
            foreach ($properties as $property => $value) {
                $entity->$property = $value;
            }
            return $entity;
        }
    }

    protected function generateFile($json, $language, $type)
    {
        $fprefix = config('media.language_batch_prefix', 'lang');
        $batchprefix = 'batch' . $this->batchStamp;
        $fname = $language . '-' . $type. $this->batchStamp .'-'. Str::random(40) . '.json';
        $filepath = implode(DIRECTORY_SEPARATOR, [
            getenv('CDN_PREFIX'),
            app('multisite')->getSite()->getCdnPrefix(),
            $fprefix,
            $batchprefix,
            $fname,
        ]);

        $stream = tmpfile();
        $fs = service('tool.filesystem');

        /**
         * Before doing anything, clean the ouput buffer and avoid garbage like unnecessary space
         * paddings in our file
         */
        if (ob_get_length()) {
            ob_clean();
        }
        /**
         * Write the JSON to the stream
         */
        fputs($stream, $json);


        // Remove any leading slashes on the filename, path is always relative.
        $filepath = ltrim($filepath, DIRECTORY_SEPARATOR);

        $config = ['mimetype' => 'text/plain'];

        $fs->putStream($filepath, $stream, $config);

        if (is_resource($stream)) {
            fclose($stream);
        }

        $size = $fs->getSize($filepath);
        $type = $fs->getMimetype($filepath);
        echo OutputText::info("Created file $filepath");

        return new FileData([
            'file' => $filepath,
            'type' => $type,
            'size' => $size,
        ]);
    }
}
