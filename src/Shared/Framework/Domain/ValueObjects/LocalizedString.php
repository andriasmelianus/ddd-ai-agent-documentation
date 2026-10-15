<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

use JsonException;
use Src\Shared\Framework\Domain\Enums\LanguageEnum;
use Src\Shared\Framework\Helpers\MixedHelper;

/**
 * Class LocalizedString
 *
 * Value Object untuk **string yang di-lokalisasi** ke beberapa bahasa
 * (mis. `en`, `es`). Menyimpan pasangan `language_code => translation`
 * sebagai array asosiatif, dan menyediakan konversi dua-arah ke/dari
 * JSON untuk keperluan persistensi (mis. disimpan di kolom `TEXT/JSON`).
 *
 * Mengimplementasikan:
 *  - {@see JsonPersistenceInterface} — kontrak `fromJson()` / `toJson()`.
 *  - {@see ValueObjectInterface} — marker VO agar dikenali framework.
 *
 * Kunci lokalisasi berasal dari {@see LanguageEnum::$value} (string
 * seperti `english`, `spanish`, dst).
 */
final class LocalizedString implements JsonPersistenceInterface, ValueObjectInterface
{
    /**
     * Map bahasa ke nilai terjemahan.
     *
     * Contoh: `['en' => 'Hello', 'es' => 'Hola']`.
     *
     * @var string[]
     */
    public array $values;

    /**
     * Membuat instance {@see LocalizedString}.
     *
     * @param  array<string>  $values  Map `language_code => translation`.
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Factory dari satu terjemahan (nullable).
     *
     * @param  string|null  $value  Terjemahan (boleh `null` — akan
     *                              mengembalikan `null` alih-alih instance).
     * @param  LanguageEnum  $language  Bahasa untuk `$value`.
     * @return self|null Instance dengan satu key `$language->value`, atau `null`.
     */
    public static function fromLanguageOrNull(?string $value, LanguageEnum $language): ?self
    {
        if ($value === null) {
            return null;
        }

        return new self([
            $language->value => $value,
        ]);
    }

    /**
     * Factory dari satu terjemahan.
     *
     * @param  string  $value  Terjemahan.
     * @param  LanguageEnum  $language  Bahasa untuk `$value`.
     * @return self Instance dengan satu key `$language->value`.
     */
    public static function fromLanguage(string $value, LanguageEnum $language): self
    {
        return new self([
            $language->value => $value,
        ]);
    }

    /**
     * Mengambil terjemahan untuk suatu bahasa.
     *
     * Menormalkan nilai literal `'null'` (string) menjadi `null` PHP —
     * berguna ketika sumber data (mis. JSON) menyimpan `null` sebagai string.
     *
     * @param  LanguageEnum  $language  Bahasa yang dicari.
     * @return string|null Terjemahan, atau `null` jika tidak tersedia.
     */
    public function get(LanguageEnum $language): ?string
    {
        $value = $this->values[$language->value] ?? null;
        if ($value === 'null') {
            return null;
        }

        return $value;
    }

    /**
     * Menetapkan terjemahan untuk suatu bahasa (mutasi in-place).
     *
     * @param  LanguageEnum  $language  Bahasa target.
     * @param  string  $value  Nilai terjemahan.
     */
    public function set(LanguageEnum $language, string $value): void
    {
        $this->values[$language->value] = $value;
    }

    /**
     * Rekonstruksi instance dari JSON string.
     *
     * Menerima input `null`, string kosong, atau literal `'null'` dan
     * mengembalikan instance kosong pada kasus tersebut.
     *
     * @param  string|null  $json  JSON payload atau `null`.
     * @return self Instance {@see LocalizedString}.
     */
    public static function fromJson(?string $json): self
    {
        if (empty($json) || $json === 'null') {
            return new self([]);
        }
        $values = MixedHelper::getStringArray($json);

        return new self($values);
    }

    /**
     * Serialisasi ke JSON dengan struktur tetap `{"en": ..., "es": ...}`.
     *
     * Terjemahan yang tidak ada akan diisi `null` pada payload.
     * Bila proses `json_encode` gagal (mis. UTF-8 invalid), mengembalikan
     * string kosong `''` — bukan melempar exception (design keputusan).
     *
     * @return string JSON string.
     */
    public function toJson(): string
    {
        try {
            return json_encode([
                'en' => $this->values['en'] ?? null,
                'es' => $this->values['es'] ?? null,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return '';
        }
    }

    /**
     * Varian {@see self::toJson()} yang mengembalikan `null` ketika
     * seluruh nilai kosong — praktis dipakai untuk kolom DB nullable.
     *
     * @return string|null JSON string, atau `null` jika semua terjemahan kosong.
     */
    public function toJsonOrNull(): ?string
    {
        if (count(array_filter($this->values)) === 0) {
            return null;
        }
        try {
            return json_encode([
                'en' => $this->values['en'] ?? null,
                'es' => $this->values['es'] ?? null,
            ], JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return '';
        }
    }

    /**
     * Cek apakah instance dianggap "kosong".
     *
     * Definisi kosong pada implementasi saat ini: **terjemahan Spanish
     * (`es`) tidak ada, bernilai literal `'null'`, atau string kosong**.
     * Bahasa lain tidak dilihat.
     *
     * @return bool `true` jika kosong menurut definisi di atas.
     */
    public function isEmpty(): bool
    {
        if (! isset($this->values['es'])) {
            return true;
        }
        if ($this->values['es'] === 'null') {
            return true;
        }

        return empty($this->values['es']);
    }
}
