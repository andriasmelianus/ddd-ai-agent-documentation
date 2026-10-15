<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\ProjectionBus;

use Src\Shared\Framework\Application\Projections\ProjectionInterface;

/**
 * Interface marker untuk Projection Handler.
 *
 * Interface ini sengaja dibuat kosong (marker interface) dan hanya
 * mendeklarasikan kontrak melalui anotasi `@method __invoke(ProjectionInterface $projection)`.
 * Setiap kelas handler diharapkan mengimplementasikan magic method `__invoke`
 * sehingga dapat dipanggil sebagai callable oleh {@see ProjectionBusInterface}.
 *
 * Pola invokable ini memungkinkan setiap handler ditulis sebagai kelas
 * single-responsibility (satu handler = satu action) dan tetap kompatibel
 * dengan mekanisme resolusi via Service Container.
 *
 * @method __invoke(ProjectionInterface $projection)
 */
interface ProjectionHandlerInterface {}
