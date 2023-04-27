<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CollectionPostRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $collection_id= $request->route('collection_id');

        if ($request->isMethod('post')) {
            return [
                'collection_id'=> [
                    'exists:sets,id'],
                'post_id' => [
                    'required',
                    'exists:posts,id',
                    Rule::unique('posts_sets')->where(function ($query) use ($collection_id) {
                        return $query->where('set_id', '=', $collection_id);
                                   //  ->where('deleted_at', null);
                    }),
                ],
            ];
        } else {
            return [];
        }
    }

    public function messages()
    {
        return [
            'post_id.required' => trans(
                'validation.not_empty',
                ['field' => trans('fields.post_id')]
            ),
            'post_id.exists' => trans(
                'validation.exists',
                ['field' => trans('fields.post_id')]
            ),
            'post_id.unique' => trans(
                //'validation.unique',
               // ['field' => trans('fields.post_id')]

                trans('fields.post_id').' is already found for this set',
            ),
        ];
    }
}
