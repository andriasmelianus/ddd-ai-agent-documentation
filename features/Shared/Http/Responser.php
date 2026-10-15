<?php

/**
 * A PHP file to simplify response content creation.
 *
 * PHP version 8
 *
 * @category File
 *
 * @author   Andrias Melianus S <andriasmelianus@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

namespace Features\Shared\Http;

use Features\Shared\Http\Contracts\ResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * A class to standardize JSON response from this system.
 *
 * PHP version 8
 *
 * @category Class
 *
 * @author   Andrias Melianus S <andriasmelianus@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class Responser implements ResponseContract
{
    // Constants.
    private const REPLACE_PREVIOUS = false;

    private const INITIAL_VALUE = null;

    public const INDEX_TOKEN = 'token';

    public const INDEX_DATA = 'data';

    public const INDEX_MESSAGE = 'message';

    public const INDEX_VALIDATION_MESSAGE = 'validation_message';

    public const INDEX_ERROR_MESSAGE = 'error_message';

    public const INDEX_STATUS = 'status';

    public const INDEX_RESPONSE_CODE = 'response_code';

    public const STATUS_SUCCESS = 'success';

    public const STATUS_DUPLICATE = 'duplicate';

    public const STATUS_FORBIDDEN = 'forbidden';

    public const STATUS_NOT_FOUND = 'not_found';

    public const STATUS_CONFLICT = 'conflict';

    public const STATUS_FAIL = 'failed';

    // Properties.
    private $headers;

    private $token;

    private $data;

    private $message;

    private $validationMessage;

    private $errorMessage;

    private $status;

    private $responseCode;

    private $httpStatus;

    /**
     * Constructor.
     *
     * @param  array  $headers  Response headers.
     * @return void
     */
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
        $this->status = self::STATUS_SUCCESS;
        $this->httpStatus = Response::HTTP_OK;

        $this->token = self::INITIAL_VALUE;
        $this->data = self::INITIAL_VALUE;
        $this->message = self::INITIAL_VALUE;
        $this->validationMessage = self::INITIAL_VALUE;
        $this->errorMessage = self::INITIAL_VALUE;
        $this->responseCode = self::INITIAL_VALUE;
    }

    /**
     * Set headers value.
     *
     * @param  array  $headers  Headers value.
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get headerrs value.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set token value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  string  $token  Token value.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setToken(string $token, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->token == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->token = $token;
        }

        return $this;
    }

    /**
     * Get token value.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Set data value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $data  Data to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setData(mixed $data, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->data == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Get the data value.
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * Set message content.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $message  Message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setMessage(mixed $message, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->message == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->message = $message;
        }

        return $this;
    }

    /**
     * Get message content.
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * Set validation message value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $validationMessage  Validation message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setValidationMessage(mixed $validationMessage, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->validationMessage == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->validationMessage = $validationMessage;
        }

        return $this;
    }

    /**
     * Get validation message value.
     */
    public function getValidationMessage(): mixed
    {
        return $this->validationMessage;
    }

    /**
     * Set error message value.
     * Value will be set at the first call, unless $replacePrevious is true.
     *
     * @param  mixed  $errorMessage  Error message to be sent.
     * @param  bool  $replacePrevious  Replace previous value.
     */
    public function setErrorMessage(mixed $errorMessage, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->errorMessage == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->errorMessage = $errorMessage;
        }

        return $this;
    }

    /**
     * Get error message value.
     */
    public function getErrorMessage(): mixed
    {
        return $this->errorMessage;
    }

    /**
     * Get status value.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the response status as success.
     * HTTP status equivalent to 200.
     */
    public function setAsSuccess(): self
    {
        $this->status = self::STATUS_SUCCESS;
        $this->httpStatus = Response::HTTP_OK;

        return $this;
    }

    /**
     * Set the response status as created.
     * HTTP status equivalent to 201.
     */
    public function setAsCreated(): self
    {
        $this->status = self::STATUS_SUCCESS;
        $this->httpStatus = Response::HTTP_CREATED;

        return $this;
    }

    /**
     * Set the response status as unauthorized.
     * HTTP status equivalent to 401.
     */
    public function setAsUnauthorized(): self
    {
        $this->status = self::STATUS_FORBIDDEN;
        $this->httpStatus = Response::HTTP_UNAUTHORIZED;

        return $this;
    }

    /**
     * Set the response status as duplicate.
     * HTTP status equivalent to 422.
     */
    public function setAsDuplicate(): self
    {
        $this->status = self::STATUS_DUPLICATE;
        $this->httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this;
    }

    /**
     * Set the response status as forbidden.
     * HTTP status equivalent to 403.
     */
    public function setAsForbidden(): self
    {
        $this->status = self::STATUS_FORBIDDEN;
        $this->httpStatus = Response::HTTP_FORBIDDEN;

        return $this;
    }

    /**
     * Set the response status as not found.
     */
    public function setAsNotFound(): self
    {
        $this->status = self::STATUS_NOT_FOUND;
        $this->httpStatus = Response::HTTP_NOT_FOUND;

        return $this;
    }

    /**
     * Set the response status as conflict.
     * HTTP status equivalent to 422.
     */
    public function setAsConflict(): self
    {
        $this->status = self::STATUS_CONFLICT;
        $this->httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this;
    }

    /**
     * Set the response status as validation fails.
     * HTTP status equivalent to 422.
     */
    public function setAsValidationFails(): self
    {
        $this->status = self::STATUS_FAIL;
        $this->httpStatus = Response::HTTP_UNPROCESSABLE_ENTITY;

        return $this;
    }

    /**
     * Set the response status as fail.
     * HTTP status equivalent to 400.
     */
    public function setAsRequestFailure(): self
    {
        $this->status = self::STATUS_FAIL;
        $this->httpStatus = Response::HTTP_BAD_REQUEST;

        return $this;
    }

    /**
     * Set the response status as server error/failure.
     * HTTP status equivalent to 500.
     */
    public function setAsServerFailure(): self
    {
        $this->status = self::STATUS_FAIL;
        $this->httpStatus = Response::HTTP_INTERNAL_SERVER_ERROR;

        return $this;
    }

    /**
     * Set the response code value.
     */
    public function setResponseCode(string $responseCode, bool $replacePrevious = self::REPLACE_PREVIOUS): self
    {
        $shouldReplacePrevious = $replacePrevious || $this->responseCode == self::INITIAL_VALUE;
        if ($shouldReplacePrevious) {
            $this->responseCode = $responseCode;
        }

        return $this;
    }

    /**
     * Get the response code value.
     *
     * @return void
     */
    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }

    /**
     * Get the structured response content.
     * NOTE: Adjust the content structure according to your need here!
     */
    public function getContent(): array
    {
        $content = [];
        $content[self::INDEX_STATUS] = $this->status;
        $content[self::INDEX_RESPONSE_CODE] = $this->responseCode;
        $content[self::INDEX_MESSAGE] = $this->message;
        $content[self::INDEX_DATA] = $this->data;
        if ($this->token) {
            $content[self::INDEX_TOKEN] = $this->token;
        }
        if ($this->validationMessage) {
            // NOTE: Follow Borwita response format.
            $content[self::INDEX_MESSAGE] = $this->validationMessage;
        }
        if ($this->errorMessage) {
            // NOTE: Follow Borwita response format.
            $content[self::INDEX_MESSAGE] = $this->errorMessage;
        }

        // Only show error message on production
        if (config('app.env') === 'production') {
            if ($this->httpStatus == Response::HTTP_INTERNAL_SERVER_ERROR) {
                $content[self::INDEX_MESSAGE] = 'Internal Server Error';
            }
        }

        return $content;
    }

    /**
     * Create JSON response ready to be returned by controller.
     */
    public function send(?int $httpStatus = null): JsonResponse
    {
        return response()
            ->json($this->getContent(), $httpStatus ?? $this->httpStatus, $this->headers);
    }
}
