<?php

namespace Features\Shared\Http\Contracts;

use Illuminate\Http\JsonResponse;

interface ResponseContract
{
    // Constants: INDEX
    public const INDEX_TOKEN = 'token';

    public const INDEX_DATA = 'data';

    public const INDEX_MESSAGE = 'message';

    public const INDEX_VALIDATION_MESSAGE = 'validation_message';

    public const INDEX_ERROR_MESSAGE = 'error_message';

    public const INDEX_STATUS = 'status';

    public const INDEX_RESPONSE_CODE = 'response_code';

    // Constants: STATUS
    public const STATUS_SUCCESS = 'success';

    public const STATUS_DUPLICATE = 'duplicate';

    public const STATUS_FORBIDDEN = 'forbidden';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_CONFLICT = 'conflict';

    public const STATUS_FAIL = 'failed';

    /**
     * Set headers value.
     *
     * @param  array  $headers  Headers value.
     */
    public function setHeaders(array $headers): self;

    /**
     * Get headers value.
     */
    public function getHeaders(): array;

    /**
     * Set token value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  string  $token  Token value.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setToken(string $token, bool $replacePrevious = false): self;

    /**
     * Get token value.
     */
    public function getToken(): ?string;

    /**
     * Set data value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $data  Data to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setData(mixed $data, bool $replacePrevious = false): self;

    /**
     * Get the data value.
     */
    public function getData(): mixed;

    /**
     * Set message content.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $message  Message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setMessage(mixed $message, bool $replacePrevious = false): self;

    /**
     * Get message content.
     */
    public function getMessage(): mixed;

    /**
     * Set validation message value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $validationMessage  Validation message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setValidationMessage(mixed $validationMessage, bool $replacePrevious = false): self;

    /**
     * Get validation message value.
     */
    public function getValidationMessage(): mixed;

    /**
     * Set error message value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $errorMessage  Error message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setErrorMessage(mixed $errorMessage, bool $replacePrevious = false): self;

    /**
     * Get error message value.
     */
    public function getErrorMessage(): mixed;

    /**
     * Get status value.
     */
    public function getStatus(): string;

    /**
     * Set the response status as success.
     * HTTP status equivalent to 200.
     */
    public function setAsSuccess(): self;

    /**
     * Set the response status as created.
     * HTTP status equivalent to 201.
     */
    public function setAsCreated(): self;

    /**
     * Set the response status as unauthorized.
     * HTTP status equivalent to 401.
     */
    public function setAsUnauthorized(): self;

    /**
     * Set the response status as duplicate.
     * HTTP status equivalent to 422.
     */
    public function setAsDuplicate(): self;

    /**
     * Set the response status as forbidden.
     * HTTP status equivalent to 403.
     */
    public function setAsForbidden(): self;

    /**
     * Set the response status as not found.
     */
    public function setAsNotFound(): self;

    /**
     * Set the response status as conflict.
     * HTTP status equivalent to 422.
     */
    public function setAsConflict(): self;

    /**
     * Set the response status as validation fails.
     * HTTP status equivalent to 422.
     */
    public function setAsValidationFails(): self;

    /**
     * Set the response status as fail.
     * HTTP status equivalent to 400.
     */
    public function setAsRequestFailure(): self;

    /**
     * Set the response status as server error/failure.
     * HTTP status equivalent to 500.
     */
    public function setAsServerFailure(): self;

    /**
     * Set the response code value.
     */
    public function setResponseCode(string $responseCode, bool $replacePrevious = false): self;

    /**
     * Get the response code value.
     */
    public function getResponseCode(): ?string;

    /**
     * Create JSON response ready to be returned by controller.
     *
     * @param  int|null  $httpStatus  Optional HTTP status code to override default.
     */
    public function send(?int $httpStatus = null): JsonResponse;
}
