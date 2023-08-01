<?php

namespace Ushahidi\Modules\V5\Requests;

use Illuminate\Http\Request;

class ApiKeyRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request)
    {

        if ($request->isMethod('post')) {
            return $this->storeRules($request);
        } elseif ($request->isMethod('put')) {
            return $this->updateRules($request);
        } else {
            return [];
        }
    }

    private function storeRules(Request $request): array
    {
        return [
        ];
    }

    private function updateRules(Request $request): array
    {
        return [
        ];
    }


    public function messages(): array
    {
        return [
          
        ];
    }
}
