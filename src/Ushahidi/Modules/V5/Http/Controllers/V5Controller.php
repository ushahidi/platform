<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Ushahidi\Modules\V5\Models\Translation;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Common\ValidatorRunner;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Ushahidi\Modules\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controller as BaseController;
use App\Bus\Query\QueryBus;
use App\Bus\Command\CommandBus;

class V5Controller extends BaseController
{


    /**
     * The response builder callback.
     *
     * @var \Closure
     */
    protected static $responseBuilder;

    /**
     * The error formatter callback.
     *
     * @var \Closure
     */

    protected $queryBus;
    protected $commandBus;
    public function __construct(QueryBus $queryBus, CommandBus $commandBus)
    {
        $this->queryBus = $queryBus;
        $this->commandBus = $commandBus;
    }


    protected static $errorFormatter;
    /**
     * @param null $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function make403($message = null)
    {
        return response()->json(
            [
                'error' => 403,
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
                'error' => 500,
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
                'error' => 404,
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
                'error' => 422,
                'messages' => $messages,
                'type' => $type
            ],
            422
        );
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return void
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Throw the failed validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationException(
            $validator,
            $this->buildFailedValidationResponse(
                $request,
                $this->formatValidationErrors($validator)
            )
        );
    }

    /**
     * Authorize a given action against a set of arguments.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);

        return app(Gate::class)->authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);

        return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
    }

    public function authorizeAnyone($ability, $arguments = [])
    {
        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);

        return $this->authorizeForUser(Auth::user() ?? new GenericUser(['role' => 'guest']), $ability, $arguments);
    }

    /**
     * Authorize a given action for a the current user.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeForCurrentUser($ability, $arguments = [])
    {
        $gUser = $this->getGenericUser();

        list($ability, $arguments) = $this->parseAbilityAndArguments($ability, $arguments);
        return app(Gate::class)->forUser($gUser)->authorize($ability, $arguments);
    }

    public function getGenericUser()
    {
        return Auth::guard()->user();
    }

    /**
     * Guesses the ability's name if it wasn't provided.
     *
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return array
     */
    protected function parseAbilityAndArguments($ability, $arguments)
    {
        if (is_string($ability)) {
            return [$ability, $arguments];
        }

        return [debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'], $ability];
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
            'operation.required' => trans(
                'validation.required',
                ['field' => trans('bulk.operation')]
            ),
            'operation.string' => trans(
                'validation.string',
                ['field' => trans('bulk.operation')]
            ),
            'operation.in' => trans(
                'validation.in_array',
                ['field' => trans('bulk.operation')]
            ),
            'items.array' => trans(
                'validation.array',
                ['field' => trans('bulk.items')]
            ),
            'items.required' => trans(
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
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app('validator');
    }

    protected function formatValidationErrors(Validator $validator)
    {
        if (isset(static::$errorFormatter)) {
            return call_user_func(static::$errorFormatter, $validator);
        }

        return $validator->errors()->getMessages();
    }

    protected function buildFailedValidationResponse(Request $request, array $errors)
    {
        if (isset(static::$responseBuilder)) {
            return call_user_func(static::$responseBuilder, $request, $errors);
        }

        return new JsonResponse($errors, 422);
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
     * get the approved hedrate relationships
     *
     * @param  array  $relationships
     * @param Request $request
     * @return array
     */
    public function getHydrate(array $relationships, Request $request): array
    {
        if ($request->has('hydrate') && !$request->get('hydrate')) {
            $required_relationships = [];
        } elseif ($request->get('hydrate')) {
            $required_relationships = explode(',', $request->get('hydrate'));
        } else {
            $required_relationships = $relationships;
        }
        return array_filter($required_relationships, function ($o) use ($relationships) {
            return in_array($o, $relationships);
        });
    }
    
    protected function deleteResponse(int $id)
    {
        return response()->json(['result' => ['deleted' => $id]]);
    }
}
