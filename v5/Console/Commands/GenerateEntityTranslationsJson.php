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
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->generateSurveyEntities();
        $this->generateCategoryEntities();
        $this->generatePostEntities();
    }

    /**
     * @return mixed
     */
    private function getCategories()
    {
        return
            $this->attachProperties(
                Category::all(
                    array_merge(['id', 'base_language'], Category::translatableAttributes())
                ),
                ['output_type' => 'category']
            )
                ->makeHidden(['role', 'parent', 'translations'])
                ->groupBy('base_language');
    }

    /**
     * Creates JSON file with the entities relating to Categories, to be used by translators in their systems
     */
    protected function generateCategoryEntities()
    {

        echo OutputText::info("Gathering translatable Category entities.");
        /**
         * Getting all the base text for the context first sorted by either language in the case of category,
         * or their relationship in the case of stages and attributes
         * We are also hiding can_create and tasks for $survey,
         * and other relationships and appends from models that come loaded
         * by default otherwise. The same principle is applied to $stages and $attributes.
         */
        $categories = $this->getCategories();

        $languages = $this->getLanguages($categories);
        $languages->each(function ($lang) use ($categories) {
            $values = $categories
                    ->get($lang)
                    ->flatten()
                    ->toJson();
            /**
             * Generates a file per each language containing
             * all the survey related entities base text
             */
            $this->generateFile($values, $lang, 'categories');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Collection|Post[]
     */
    private function getPosts()
    {
        return Post::all(
            array_merge(['id', 'base_language'], Post::translatableAttributes())
        )
            ->makeHidden(['values', 'translations'])
            ->map(function ($post) {
                $values = $post->getTranslatablePostValues()->map(function ($value) use ($post) {
                    return $this->attachProperties($value, [
                        'output_type' => 'post_value',
                        'post_id' => $post->id,
                        'attribute_name' => $value->attribute->label
                    ])->makeHidden(['post', 'translations' , 'attribute']);
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
        /**
         * Getting all the base text for the context first sorted by either language in the case of category,
         * or their relationship in the case of stages and attributes
         * We are also hiding can_create and tasks for $survey,
         * and other relationships and appends from models that come loaded
         * by default otherwise. The same principle is applied to $stages and $attributes.
         */
        $posts = $this->getPosts();

        $this
            ->getLanguages($posts)
            ->each(function ($lang) use ($posts) {
                if (!$lang) {
                    return;
                }
                $json = Collection::make([]);
                $posts = $posts
                    ->get($lang);
                $posts->each(function ($post) use (&$json) {
                    $fieldValues = $post->fieldValues->flatten();
                    $post = $post->makeHidden(
                        [
                            'fieldValues',
                            'valuesVarchar',
                            'valuesMedia',
                            'valuesMarkdown',
                            'valuesText'
                        ]
                    );
                    $json = $json->push($post)->concat($fieldValues);
                });
                /**
                 * Generates a file per each language containing
                 * all the survey related entities base text
                 */
                $this->generateFile($json->toJson(), $lang, 'posts');
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
        $surveys = $this->attachProperties($surveys, ['output_type' => 'survey']);
        return $surveys;
    }

    /**
     * @param $surveys
     * @return \Illuminate\Database\Eloquent\Collection|Collection|Stage[]
     */
    private function getTasks($surveys)
    {
        return Stage::all(
            array_merge(['id', 'form_id'], Stage::translatableAttributes())
        )->makeHidden(['translations', 'fields', 'survey'])
        ->map(function ($task) use ($surveys) {
            return $this->attachProperties(
                $task,
                [
                    'output_type' => 'stage',
                    'base_language' => $surveys->where('id', '=', $task->form_id)->first()->base_language
                ]
            );
        });
    }

    /**
     * @param $tasks
     * @return \Illuminate\Database\Eloquent\Collection|Collection|Attribute[]
     */
    private function getAttributes($tasks)
    {
        return Attribute::all(
            array_merge(['id', 'form_stage_id'], Attribute::translatableAttributes())
        )->makeHidden(['stage', 'translations'])
        ->map(function ($attribute) use ($tasks) {
            /**
             * attaching an attribute to the model to mark the original language for the future
             */
            return $this->attachProperties($attribute, [
                'output_type' => 'attribute',
                'base_language' => $tasks->where('id', '=', $attribute->form_stage_id)->first()->base_language
            ]);
        });
    }

    /**
     * Creates JSON file with the entities relating to Surveys, to be used by translators in their systems
     */
    protected function generateSurveyEntities()
    {

        echo OutputText::info("Gathering translatable Survey entities.");
        /**
         * Getting all the base text for the context first sorted by either language in the case of survey,
         * or their relationship in the case of stages and attributes
         * We are also hiding can_create and tasks for $survey,
         * and other relationships and appends from models that come loaded
         * by default otherwise. The same principle is applied to $stages and $attributes.
         */
        $surveys = $this->getSurveys();
        $tasks = $this->getTasks($surveys);
        $attributes = $this->getAttributes($tasks)->groupBy('base_language');
        $surveys = $surveys->groupBy('base_language');
        $tasks = $tasks->groupBy('base_language');
        $languages = $this->getLanguages($surveys);
        $languages->each(function ($lang) use ($surveys, $tasks, $attributes) {
            $values = Collection::make([])
                ->concat($surveys->get($lang))
                ->concat($tasks->get($lang))
                ->concat($attributes->get($lang))
                ->flatten()
                ->toJson();
            /**
             * Generates a file per each language containing
             * all the survey related entities base text
             */
            $this->generateFile($values, $lang, 'surveys');
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
    /**
     * @param $collection
     * @return mixed
     */
    private function getLanguages($collection)
    {
        return $collection->keys()->unique()->flatten();
    }

    protected function generateFile($json, $language, $type)
    {
        $fprefix = config('media.language_batch_prefix', 'lang');
        $fname = $language . '-' . $type. Carbon::now()->format('Ymd') .'-'. Str::random(40) . '.json';
        $filepath = implode(DIRECTORY_SEPARATOR, [
            $fprefix,
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

        return new FileData([
            'file' => $filepath,
            'type' => $type,
            'size' => $size,
        ]);
    }
}
