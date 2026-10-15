<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

/**
 * Class Timestamp
 *
 * Value Object **immutable** yang membungkus informasi audit-trail
 * standar sebuah entity:
 *  - `createdBy` — user yang membuat record.
 *  - `updatedBy` — user yang terakhir memperbarui record.
 *  - `createdAt` — kapan record dibuat (UNIX epoch detik).
 *  - `updatedAt` — kapan record terakhir diperbarui (UNIX epoch detik).
 *
 * Menggunakan modifier `final readonly` (PHP 8.2+) sehingga:
 *  - Tidak dapat di-subclass — mencegah override yang bisa merusak
 *    kontrak immutability.
 *  - Semua properti hanya boleh di-set sekali di constructor.
 *
 * Semua field bersifat **nullable** untuk mengakomodasi entity yang:
 *  - Belum pernah diperbarui (misal `updatedAt`/`updatedBy` bernilai `null`).
 *  - Berasal dari sumber yang tidak memuat metadata audit.
 *
 * Dipakai oleh {@see \Src\Shared\Framework\Domain\Entities\BaseEntity::timestamps()}
 * dan {@see \Src\Shared\Framework\Domain\Entities\BaseEntity::hydrateTimestamps()}.
 */
final readonly class Timestamp
{
    /**
     * Membuat instance {@see Timestamp} baru.
     *
     * @param  string|null  $createdBy  Identitas user pembuat record (nullable).
     * @param  string|null  $updatedBy  Identitas user yang memperbarui record (nullable).
     * @param  int|null  $createdAt  UNIX epoch detik saat record dibuat (nullable).
     * @param  int|null  $updatedAt  UNIX epoch detik saat record diperbarui (nullable).
     */
    public function __construct(
        public ?string $createdBy,
        public ?string $updatedBy,
        public ?int $createdAt,
        public ?int $updatedAt,
    ) {}
}
