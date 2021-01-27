<?php

namespace v5\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Lumen\Routing\Controller as BaseController;
use Ushahidi\App\Auth\GenericUser;
use Ushahidi\App\Formatter\Collection;
use v5\Models\Translation;
use v5\Common\ValidatorRunner;

class V5Controller extends BaseController
{
    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make403($message = null)
    {
        return response()->json(
            [
                'error'   => 403,
                'message' => $message ?? trans('errors.generic403'),
            ],
            403
        );
    }
    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make500($message = null)
    {
        return response()->json(
            [
                'error'   => 500,
                'message' => $message ?? trans('errors.generic500'),
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
     * @param $input
     * @return array
     */
    protected function bulkGetIds(array $input)
    {
        return \Illuminate\Support\Collection::make($input)
            ->pluck('id')->toArray();
    }
    /**
     * @param $input
     * @return \Illuminate\Support\Collection
     */
    protected function bulkGetFields(array $input, array $fields)
    {
        return \Illuminate\Support\Collection::make($input)->map(function ($item) use ($fields) {
            return Arr::only($item, $fields);
        });
    }

    protected function bulkValidateEnvelope($data)
    {
        return ValidatorRunner::runValidation(
            $data,
            $this->getBulkEnvelopeValidationRules(),
            $this->getBulkEnvelopeValidationMessages()
        );
    }

    protected function getBulkEnvelopeValidationRules()
    {
        // our rules
        return [
            'operation' => [
                'required',
                'string',
                Rule::in(['patch', 'delete'])
            ],
            'items' => [
                'array',
                'required'
            ]
        ];
    }

    protected function getBulkEnvelopeValidationMessages()
    {
        return [
            'operation.required'                      => trans(
                'validation.required',
                ['field' => trans('bulk.operation')]
            ),
            'operation.string'                      => trans(
                'validation.string',
                ['field' => trans('bulk.operation')]
            ),
            'operation.in'                      => trans(
                'validation.in_array',
                ['field' => trans('bulk.operation')]
            ),
            'items.array'                             => trans(
                'validation.array',
                ['field' => trans('bulk.items')]
            ),
            'items.required'                          => trans(
                'validation.not_empty',
                ['field' => trans('bulk.items')]
            ),
        ];
    }

    /**
     * @param $key
     * @param $inputValue
     * @return array
     */
    protected function getField($key, $inputValue)
    {
        if (in_array($key, $this->ignoreInput())) {
            return null;
        }
        return $inputValue;
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
        if (empty($translation_input)) {
            return [];
        }
        Translation::where('translatable_id', $translatable_id)->where('translatable_type', $type)->delete();
        return $this->saveTranslations($entity, $entity_array, $translation_input, $translatable_id, $type);
    }//end updateTranslations()
}
