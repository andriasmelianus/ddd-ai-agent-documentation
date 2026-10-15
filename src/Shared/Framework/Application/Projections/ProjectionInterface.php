<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Application\Projections;

/**
 * Marker interface untuk Projection dalam pola CQRS / Event Sourcing.
 *
 * Projection adalah representasi read-model yang dibangun dari serangkaian
 * event domain atau perubahan state, dan dikonsumsi oleh sisi query aplikasi.
 * Kelas yang meng-implement interface ini akan dikenali oleh
 * {@see \Src\Shared\Framework\Infrastructure\Bus\ProjectionBus\ProjectionBusInterface}
 * sebagai payload yang dapat diproyeksikan ke handler yang sesuai.
 *
 * Interface sengaja dibiarkan kosong (marker interface) agar tidak memaksakan
 * kontrak metode tertentu pada projection konkret.
 */
interface ProjectionInterface {}
