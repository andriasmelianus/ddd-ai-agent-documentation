<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Enums;

/**
 * Enum UnsetEnum
 *
 * Enum sentinel (marker) yang digunakan untuk merepresentasikan kondisi
 * "belum di-set" atau "sengaja tidak diberi nilai" pada suatu properti
 * atau parameter — berbeda dengan `null` yang biasanya berarti "tidak
 * ada nilai".
 *
 * Pola ini berguna misalnya pada DTO/Command yang membedakan antara:
 *  - properti yang **tidak dikirim** oleh klien (perlu dianggap `unset`
 *    dan tidak boleh diperbarui), versus
 *  - properti yang **dikirim dengan nilai `null`** (perlu diperbarui
 *    menjadi `null`).
 *
 * Contoh penggunaan:
 * ```php
 * public function __construct(
 *     public string|UnsetEnum $name = UnsetEnum::unset,
 *     public ?string          $email = null,
 * ) {}
 * ```
 */
enum UnsetEnum: string
{
    /** @var string Menandakan bahwa nilai belum/tidak di-set. */
    case unset = 'unset';
}
