<?php

namespace v5\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Controller as BaseController;
use Ushahidi\App\Auth\GenericUser;
use v5\Models\Translation;

class V4Controller extends BaseController
{
    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make500($message = null)
    {
        return response()->json(
            [
                'error'   => 500,
                'message' => $message ?? 'Not found',
            ],
            500
        );
    }

    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make404($message = null)
    {
        return response()->json(
            [
                'error'   => 404,
                'message' => $message ?? 'Not found',
            ],
            404
        );
    }

    /**
     * @param $messages
     * @param string $type (can be entity or translation)
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make422($messages, $type = 'entity')
    {
        return response()->json(
            [
                'error'   => 422,
                'messages' => $messages,
                'type' => $type
            ],
            422
        );
    }

    public function authorizeAnyone($ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        return $this->authorizeForUser(Auth::user() ?? new GenericUser(['role' => 'guest']), $ability, $arguments);
    }

    /**
     * Not all fields are things we want to allow on the body of requests
     * an author won't change after the fact so we limit that change
     * to avoid issues from the frontend.
     * @return string[]
     */
    protected function ignoreInput()
    {
        return [];
    }

    /**
     * @param $input
     * @return array
     */
    protected function getFields($input)
    {
        $return = $input;
        $ignore = $this->ignoreInput();
        foreach ($input as $key => $item) {
            if (in_array($key, $ignore)) {
                unset($return[$key]);
            }
        }
        return $return;
    }


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
            $validation_errors = $this->validateTranslations($entity, $entity_array, $translations);
            if (!empty($validation_errors)) {
                $errors[$language] = $validation_errors;
                continue;
            }
            foreach ($translations as $key => $translated) {
                if (is_array($translated)) {
                    $translated = json_encode($translated);
                }

                $t = Translation::create(
                    [
                        'translatable_type' => $type,
                        'translatable_id'   => $translatable_id,
                        'translated_key'    => $key,
                        'translation'       => $translated,
                        'language'          => $language,
                    ]
                );
            }
        }
        return $errors;
    }//end saveTranslations()

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
        if (!is_array($translation_input)) {
            return [];
        }
        Translation::where('translatable_id', $translatable_id)->where('translatable_type', $type)->delete();
        return $this->saveTranslations($entity, $entity_array, $translation_input, $translatable_id, $type);
    }//end updateTranslations()
}
