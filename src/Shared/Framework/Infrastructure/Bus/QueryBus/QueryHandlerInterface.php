<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\QueryBus;

/**
 * Marker interface untuk Query Handler.
 *
 * Interface ini sengaja dibuat kosong dan hanya mendeklarasikan kontrak
 * melalui anotasi `@method mixed __invoke(QueryInterface $query)`.
 * Setiap kelas handler diharapkan mengimplementasikan magic method
 * `__invoke($query)` agar dapat dipanggil sebagai callable oleh {@see QueryBus}.
 *
 * Pola invokable ini mendukung prinsip single-responsibility: satu handler
 * menangani satu jenis Query, dan resolusinya dilakukan otomatis oleh
 * Service Container Laravel (via `App::make()`).
 *
 * @method mixed __invoke(QueryInterface $query)
 */
interface QueryHandlerInterface {}
