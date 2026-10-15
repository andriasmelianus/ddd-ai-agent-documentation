<?php

declare(strict_types=1);

namespace Features\Shared\Storage;

use Src\Shared\Infrastructure\Dirman\Dirman;

final class CompanyStorage
{
    private const BASE_STRUCTURE = ['ERP'];

    private const IMAGE_DIRECTORY = 'company-image';

    public static function getCompanyImagePath(?string $filename = null, int $moveToUpperLevelCount = 0): string
    {
        return (new Dirman(isPublic: true))
            ->setBaseStructure(self::BASE_STRUCTURE)
            ->appendStructure(self::IMAGE_DIRECTORY)
            ->getPath($filename, $moveToUpperLevelCount);
    }
}
