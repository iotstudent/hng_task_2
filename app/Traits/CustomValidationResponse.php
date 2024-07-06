<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;

trait CustomValidationResponse

{
    protected function formatValidationErrors(ValidatorContract $validator)
    {
        $errors = [];

        foreach ($validator->errors()->getMessages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }

        throw new HttpResponseException(
            response()->json([
                'errors' => $errors,
            ], 422)
        );
    }

    protected function validateWithCustomResponse(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->formatValidationErrors($validator);
        }
    }
}
