<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\ProjectionBus;

use RuntimeException;
use Src\Shared\Framework\Application\Projections\ProjectionInterface;
use Src\Shared\Framework\Helpers\Container;

use function get_class;

/**
 * Implementasi sederhana {@see ProjectionBusInterface}.
 *
 * Kelas ini me-resolve ProjectionHandler yang sesuai untuk sebuah Projection
 * berdasarkan dua strategi (dievaluasi berurutan):
 *
 *  1. **Routing manual** melalui properti publik {@see SimpleProjectionBus::$routes}
 *     yang memetakan `class-string<ProjectionInterface>` ke `class-string<ProjectionHandlerInterface>`.
 *  2. **Konvensi penamaan**: mengganti akhiran `Projection` pada nama kelas menjadi `Handler`
 *     (mis. `SyncUserProjection` → `SyncUserHandler`).
 *
 * Setelah nama handler diperoleh, instance-nya di-resolve dari Service Container
 * via {@see Container::getObjectInstance()} lalu dipanggil sebagai invokable
 * (`__invoke($projection)`). Nilai balik metode {@see SimpleProjectionBus::project()}
 * mengikuti nilai balik handler.
 *
 * Kelas ini bersifat `final` — untuk perilaku berbeda, disarankan membuat
 * implementasi baru dari {@see ProjectionBusInterface}.
 */
final class SimpleProjectionBus implements ProjectionBusInterface
{
    /**
     * Pemetaan routing manual dari kelas Projection ke kelas ProjectionHandler.
     *
     * Bila entri untuk kelas Projection tersedia, akan digunakan sebagai
     * prioritas utama sebelum konvensi penamaan otomatis.
     *
     * @var array<class-string<ProjectionInterface>, class-string<ProjectionHandlerInterface>>
     */
    public array $routes = [];

    /**
     * Memproyeksikan Projection ke handler yang sesuai lalu mengembalikan hasilnya.
     *
     * Alur eksekusi:
     *  1. Ambil nama kelas Projection via `get_class()`.
     *  2. Cari handler dari `$this->routes[$projectionClass]`; jika tidak ada,
     *     terapkan konvensi penamaan `.../XxxProjection` → `.../XxxHandler`
     *     menggunakan `preg_replace('/Projection$/', 'Handler', ...)`.
     *  3. Jika hasil konvensi juga `null` (tidak match), lempar {@see RuntimeException}.
     *  4. Resolve instance handler via {@see Container::getObjectInstance()}
     *     dan panggil `__invoke($projection)`.
     *
     * @param  ProjectionInterface  $projection  Objek Projection yang akan diproses.
     * @return mixed Nilai balik dari handler yang dieksekusi.
     *
     * @throws RuntimeException Bila tidak ada handler yang dapat di-resolve untuk kelas Projection.
     */
    public function project(ProjectionInterface $projection): mixed
    {
        $projectionClass = get_class($projection);
        /** @var class-string<ProjectionHandlerInterface>|null $projectionHandlerName */
        $projectionHandlerName = $this->routes[$projectionClass] ?? preg_replace('/Projection$/', 'Handler', $projectionClass);
        if ($projectionHandlerName === null) {
            throw new RuntimeException('Handler not found for '.$projectionClass);
        }

        /** @var ProjectionHandlerInterface $projectionHandler */
        $projectionHandler = Container::getObjectInstance($projectionHandlerName);

        return $projectionHandler->__invoke($projection);
    }
}
