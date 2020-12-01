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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Util\MimeType;
use Symfony\Component\Console\Output\ConsoleOutput;
use Ushahidi\App\Tools\OutputText;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Ushahidi\Core\Tool\FileData;
use v5\Models\Attribute;
use v5\Models\Category;
use v5\Models\Post;
use v5\Models\Stage;
use v5\Models\Survey;use Illuminate\Support\Facades\File as LocalFilesystem;
use v5\Models\Translation;


class ImportEntityTranslationsJson extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'entitytranslations:in';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest a JSON file per language with all entity source texts.';
    protected $signature = 'entitytranslations:in {target-language} {file-path}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $target_language = $this->argument('target-language');
        $file_path = $this->argument('file-path');
        $file_exists = Storage::disk('local')->exists($file_path);
        if (!$file_exists) {
            echo OutputText::error("File does not exist " . Storage::disk('local')->url($file_path) );
            return;
        }

        $json = json_decode(Storage::disk('local')->get($file_path));
        if (json_last_error()) {
            echo OutputText::error(
                "The JSON content in $file_path is invalid. Please check the format. Error reported: " . json_last_error_msg()
            );
            return;
        }
        $translate_items = $this->saveTranslations($json, $target_language, $file_path);
        if (isset($translate_items["errors"])) {
            foreach ($translate_items["errors"] as $error) {
                echo OutputText::error($error);
            }
        } else {
            echo OutputText::info(
                "Created translations for {$translate_items['translated']} out of " .
                "{$translate_items['available']} items found in the file."
            );
        }
    }

    private function saveTranslations($json, $target_language, $file_name) {
        $collection = Collection::make($json);
        $last = null;
        try {
            DB::beginTransaction();
            $count = 0;
            $collection->each(function ($item) use ($target_language, &$last, &$count) {
                $last = $item;
                if (!$this->isValidItem($item)) {
                    throw new \Exception("Some items are missing required fields. First error found in : " . json_encode($item));
                }
                if (!$item->translation) {
                    echo OutputText::warn(
                        "Item with id:'$item->id', output_type:'$item->output_type', attribute_name: '$item->attribute_name' " .
                        "does not have a translation. Ignoring."
                    );
                    return;
                }
                Translation::where('translatable_id', $item->id)
                            ->where('translatable_type', $item->output_type)
                            ->where('language', $target_language)
                            ->delete();

                Translation::create([
                    'translatable_id' => $item->id,
                    'translated_key' => $item->attribute_name, //
                    'translatable_type' => $item->output_type,
                    'translation' => $item->translation,
                    'language' => $target_language
                ]);
                $count++;
            });
            DB::commit();
            return ['available' => $collection->count(), 'translated' => $count];
        } catch (\Exception $e) {
            DB::rollback();
            $errors = [
                "The import process was cancelled and reverted for the file $file_name due to an error. " .
                "No translations were saved from that file."
            ];
            if ($last && $this->isValidItem($last)) {
                array_push(
                $errors,
                "The error happened while processing the item with id: $last->id and output_type: $last->output_type."
                );
            }
            array_push($errors, "Exception message: {$e->getMessage()}");
            return ["errors" => $errors];
        }
    }
    private function isValidItem($item) {
        return isset($item->id) && isset($item->output_type);
    }
}
