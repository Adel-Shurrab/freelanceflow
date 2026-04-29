<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseFormRequest extends FormRequest
{
    /**
     * Allow requests by default.
     * Override this method in child requests for policy checks.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return API-formatted JSON on validation failure.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors()->toArray()),
        );
    }

    /**
     * Return API-formatted JSON on authorization failure.
     */
    protected function failedAuthorization(): never
    {
        throw new HttpResponseException(
            ApiResponse::forbidden('You are not authorized to perform this action.'),
        );
    }
}
