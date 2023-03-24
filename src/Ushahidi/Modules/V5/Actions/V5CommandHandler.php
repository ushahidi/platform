<?php

namespace Ushahidi\Modules\V5\Actions;

use App\Bus\Command\AbstractCommandHandler;
use Ushahidi\Modules\V5\Models\Translation;

abstract class V5CommandHandler extends AbstractCommandHandler
{

    /**
     * @param $entity
     * @param array $entity_array
     * @param array $translations
     * @return array
     */
    public function validateTranslations($entity, $entity_array, array $translations)
    {
        $entity_array = array_merge($entity_array, $translations);
        if (!$entity->validate($entity_array)) {
            return $entity->errors->toArray();
        }
        return [];
    }

    /**
     * @param $entity (ie Category, Post, etc)
     * @param array $entity_array
     * @param array $translation_input
     * @param int $translatable_id
     * @param string $type
     * @return array
     */
    protected function saveTranslations(
        $entity,
        array $entity_array,
        array $translation_input,
        int $translatable_id,
        string $type
    ) {
        if (!is_array($translation_input)) {
            return [];
        }

        $errors = [];
        foreach ($translation_input as $language => $translations) {
            // $validation_errors = $this->validateTranslations($entity, $entity_array, $translations);
            // if (!empty($validation_errors)) {
            //     $errors[$language] = $validation_errors;
            //     continue;
            // }
            foreach ($translations as $key => $translated) {
                if (is_array($translated)) {
                    $translated = json_encode($translated);
                }

                $t = Translation::create(
                    [
                        'translatable_type' => $type,
                        'translatable_id' => $translatable_id,
                        'translated_key' => $key,
                        'translation' => $translated,
                        'language' => $language,
                    ]
                );
            }
        }
        return $errors;
    } //end saveTranslations()

    /**
     * @param $entity
     * @param array $entity_array
     * @param array $translation_input
     * @param int $translatable_id
     * @param string $type
     * @return array
     */
    protected function updateTranslations(
        $entity,
        array $entity_array,
        array $translation_input,
        int $translatable_id,
        string $type
    ) {
        if (empty($translation_input)) {
            return [];
        }
        Translation::where('translatable_id', $translatable_id)->where('translatable_type', $type)->delete();
        return $this->saveTranslations($entity, $entity_array, $translation_input, $translatable_id, $type);
    } //end updateTranslations()

    /**
     * reomve the translations for translatable entity
     * @param int $translatable_id
     * @param string $type
     */
    protected function deleteTranslations(int $translatable_id, string $type)
    {
        Translation::where('translatable_id', '=', $translatable_id)
            ->where('translatable_type', $type)
            ->delete();
    } /**
      * reomve the translations for the list of translatables
      * @param array $translatable_ids
      * @param string $type
      */
    protected function deleteListTranslations(array $translatable_ids, string $type)
    {
        Translation::whereIn('translatable_id', $translatable_ids)
            ->where('translatable_type', $type)
            ->delete();
    }
}
