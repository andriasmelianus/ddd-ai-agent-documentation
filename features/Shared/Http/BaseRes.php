<?php

declare(strict_types=1);

namespace Features\Shared\Http;

use Src\Shared\Http\BaseRes as SrcBaseRes;

/**
 * Class BaseRes
 *
 * Base Resource class for HTTP responses in the Features layer.
 * Extends BaseRes from the Src\Shared\Http namespace.
 */
readonly class BaseRes extends SrcBaseRes {}

class_alias(BaseRes::class, 'Features\Shared\Http\BaseResource');
