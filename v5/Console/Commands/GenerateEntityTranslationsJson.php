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
        echo OutputText::info("Gathering translatable entities.");
        echo OutputText::info(json_encode(Survey::translatableAttributes()));
        /**
         * Getting all the base text for the context first sorted by either language in the case of survey,
         * or their relationship in the case of stages and attributes
         * We are also hiding can_create and tasks for $survey,
         * and other relationships and appends from models that come loaded
         * by default otherwise. The same principle is applied to $stages and $attributes.
         */
        $surveys = Survey::all(
            array_merge(['id', 'base_language'], Survey::translatableAttributes())
        )
                        ->makeHidden('can_create')
                        ->makeHidden('tasks');
        $stages_by_survey = Stage::
                all(
                    array_merge(['id', 'form_id'], Stage::translatableAttributes())
                )
                ->makeHidden('translations')
                ->makeHidden('fields')
                ->makeHidden('survey');
        $attributes = Attribute::all(
            array_merge(['id', 'form_stage_id'], Attribute::translatableAttributes())
        )
                ->makeHidden('stage')
                ->makeHidden('translations');

        $surveys = $surveys->map(function ($survey) {
            $survey->output_type = 'survey';
            return $survey;
        });

        $stages = $stages_by_survey->map(function ($stage) use ($surveys) {
                /**
                 * attaching an attribute to the model to mark the original language for the future
                 */
                $stage->output_type = 'stage';
                $stage->base_language = $surveys->where('id', '=', $stage->form_id)->first()->base_language;
                return $stage;
        });

        $attributes = $attributes->map(function ($attribute) use ($stages) {
                /**
                 * attaching an attribute to the model to mark the original language for the future
                 */
                $attribute->output_type = 'attribute';
                $attribute->base_language = $stages
                                                ->where('id', '=', $attribute->form_stage_id)
                                                ->first()
                                                ->base_language;
                return $attribute;
        })->groupBy('base_language');
        $surveys = $surveys->groupBy('base_language');
        $stages = $stages->groupBy('base_language');
        $languages = $stages
                        ->keys()
                        ->concat($surveys->keys())
                        ->concat($attributes->keys())
                        ->unique()
                        ->toArray();

        foreach ($languages as $lang) {
            $values = Collection::make([])
                ->concat($surveys->get($lang))
                ->concat($stages->get($lang))
                ->concat($attributes->get($lang))
                ->flatten()
                ->toJson();
            /**
             * Generates a file per each language containing
             * all the survey related entities base text
             */
            $this->generateFile($values, $lang);
        }
    }

    protected function generateFile($json, $language)
    {
        $fprefix = config('media.language_batch_prefix', 'lang');
        $fname = $language . '-' . Carbon::now()->format('Ymd') .'-'. Str::random(40) . '.json';
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
