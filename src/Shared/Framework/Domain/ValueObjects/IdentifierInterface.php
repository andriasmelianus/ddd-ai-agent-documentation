<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

/**
 * Interface IdentifierInterface
 *
 * Kontrak umum untuk seluruh **Identifier Value Object** (Ulid, Uuid,
 * IntegerIdentifier, StringIdentifier, dsb) di framework ini.
 *
 * Setiap Identifier wajib memiliki:
 *  - Method factory statis {@see self::create()} yang menerima nilai
 *    mentah (int atau string) dan mengembalikan instance ter-validasi.
 *  - Method {@see self::getValue()} untuk mengekstrak nilai primitif
 *    yang dibungkus.
 *
 * Kontrak ini dipakai oleh {@see BaseEntityHydrater} untuk mengenali
 * properti yang bertipe Identifier saat melakukan hydration otomatis
 * dari array/DB row.
 */
interface IdentifierInterface
{
    /**
     * Factory statis untuk membuat instance Identifier baru.
     *
     * Implementasi wajib melakukan validasi terhadap `$value` dan
     * melempar exception (mis.
     * {@see \Src\Shared\Framework\Domain\Exceptions\InvalidIdentifierException})
     * apabila nilai tidak valid.
     *
     * @param  string|int  $value  Nilai mentah Identifier.
     * @return static Instance Identifier yang telah tervalidasi.
     */
    public static function create(string|int $value): self;

    /**
     * Mengembalikan nilai primitif yang dibungkus oleh Identifier ini.
     *
     * @return int|string Nilai internal Identifier (tipe bergantung
     *                    pada implementasi konkret).
     */
    public function getValue(): int|string;
}
