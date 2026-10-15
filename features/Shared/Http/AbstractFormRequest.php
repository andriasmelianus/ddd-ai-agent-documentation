<?php

declare(strict_types=1);

namespace Features\Shared\Http;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractFormRequest
 *
 * Base FormRequest for HTTP requests in the Features layer.
 * Enforces type-based validation via strongly-typed DTOs instead of Laravel rules().
 */
abstract class AbstractFormRequest extends FormRequest
{
    private ?FormRequestHelper $helper = null;

    /**
     * Return a strongly-typed DTO built from HTTP input.
     */
    abstract public function getDto(): object;

    /**
     * Business and format validation lives in Handlers and Value Objects.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt by returning a standardized BaseRes response.
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        $response = BaseRes::error(
            message: 'Data validation failed',
            status: BaseRes::STATUS_FAIL,
            responseCode: null,
            errors: $validator->errors()->toArray(),
            data: null,
        );

        throw new HttpResponseException(
            response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Get the FormRequestHelper instance.
     */
    public function getHelper(): FormRequestHelper
    {
        if ($this->helper === null) {
            $this->helper = new FormRequestHelper($this);
        }

        return $this->helper;
    }
}
