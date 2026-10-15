<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\QueryBus;

/**
 * Marker interface untuk objek Query dalam pola CQRS.
 *
 * Sebuah Query merepresentasikan permintaan baca (read request) yang bersifat
 * idempotent dan tidak mengubah state sistem. Objek Query biasanya berupa
 * DTO immutable yang membawa parameter/filter pencarian.
 *
 * Interface ini kosong (marker) namun menggunakan generic template
 * `@template-covariant T` untuk memungkinkan static analyzer (PHPStan/Psalm)
 * menginfer tipe hasil `T` yang akan dikembalikan oleh QueryHandler.
 * Contoh: `class GetUserByIdQuery implements QueryInterface<User>` menyatakan
 * bahwa handler-nya akan mengembalikan objek `User`.
 *
 * @template-covariant T
 */
interface QueryInterface {}
