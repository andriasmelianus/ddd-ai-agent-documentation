<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

/**
 * Interface JsonPersistenceInterface
 *
 * Kontrak untuk Value Object yang mampu **di-serialize/deserialize
 * ke/dari JSON string** untuk keperluan persistensi (mis. disimpan ke
 * kolom database bertipe TEXT/JSON).
 *
 * Berbeda dengan {@see \JsonSerializable} bawaan PHP yang hanya
 * mendefinisikan `jsonSerialize()` (untuk `json_encode()`), interface
 * ini menuntut adanya method konversi dua-arah:
 *  - {@see self::fromJson()} — factory statis dari JSON string.
 *  - {@see self::toJson()} — konversi instance ke JSON string.
 *
 * Interface ini biasanya diimplementasikan bersama
 * {@see ValueObjectInterface} pada VO kompleks (mis.
 * {@see LocalizedString}) agar dapat dipetakan langsung dari kolom DB.
 */
interface JsonPersistenceInterface
{
    /**
     * Membuat instance dari representasi JSON string.
     *
     * @param  string  $json  Payload JSON yang akan didekode.
     * @return static Instance value object hasil rekonstruksi.
     */
    public static function fromJson(string $json): self;

    /**
     * Mengubah instance saat ini menjadi JSON string.
     *
     * @return string Representasi JSON dari value object.
     */
    public function toJson(): string;
}
