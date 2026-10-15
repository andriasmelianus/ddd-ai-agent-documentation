<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Enums;

/**
 * Enum LanguageEnum
 *
 * Merepresentasikan daftar bahasa yang didukung oleh sistem.
 *
 * Enum ini menggunakan tipe backing string sehingga setiap case dapat
 * disimpan/di-serialize sebagai nilai string (misalnya ke database atau
 * payload JSON) dan dapat direkonstruksi kembali menggunakan
 * {@see self::from()} atau {@see self::tryFrom()}.
 *
 * Trait {@see TryFromOrNullTrait} ditambahkan agar tersedia helper
 * `tryFromOrNull()` yang aman terhadap nilai `null` (mengembalikan `null`
 * tanpa melempar exception ketika input null).
 *
 * Contoh penggunaan:
 * ```php
 * $lang = LanguageEnum::tryFromOrNull($request->input('lang'));
 * if ($lang === LanguageEnum::english) {
 *     // ...
 * }
 * ```
 */
enum LanguageEnum: string
{
    use TryFromOrNullTrait;

    /** @var string Bahasa Spanyol. */
    case spanish = 'spanish';

    /** @var string Bahasa Inggris. */
    case english = 'english';

    /** @var string Bahasa Prancis. */
    case french = 'french';

    /** @var string Bahasa Jerman. */
    case german = 'german';

    /** @var string Bahasa Italia. */
    case italian = 'italian';

    /** @var string Bahasa Basque (Euskera). */
    case euskera = 'euskera';

    /** @var string Bahasa Catalan. */
    case catalan = 'catalan';

    /** @var string Bahasa Jepang. */
    case japanese = 'japanese';

    /** @var string Bahasa Portugis. */
    case portuguese = 'portuguese';

    /** @var string Bahasa Ceko. */
    case czech = 'czech';

    /** @var string Bahasa Galicia. */
    case galician = 'galician';

    /** @var string Bahasa Mandarin/Tionghoa. */
    case chinese = 'chinese';

    /** @var string Bahasa Polandia. */
    case polish = 'polish';
}
