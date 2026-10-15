<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class DomainEvent
 *
 * Kelas dasar (abstract base class) untuk seluruh **Domain Event** di
 * aplikasi. Sebuah *domain event* merepresentasikan sesuatu yang penting
 * dan telah **terjadi** di dalam domain (past-tense), misalnya
 * `OrderPlaced`, `PartnerRegistered`, atau `InvoicePaid`.
 *
 * Domain event digunakan sebagai mekanisme komunikasi antar bounded
 * context/aggregate secara **loose-coupled**: aggregate cukup mencatat
 * event (via [`BaseEntity::record()`](../Entities/BaseEntity.php:254)),
 * lalu event dilepas oleh {@see \Src\Shared\Framework\Domain\Entities\BaseEntity::releaseEvents()}
 * dan disebarkan ke listener/projector melalui EventBus.
 *
 * Kelas ini juga menggunakan trait Laravel:
 *  - {@see Dispatchable} — agar event dapat langsung di-`dispatch()` lewat
 *    fasilitas event bawaan Laravel.
 *  - {@see InteractsWithSockets} — memungkinkan event dibroadcast lewat
 *    WebSocket (mis. Laravel Echo) sekaligus mengecualikan sender.
 *
 * Kontrak yang wajib dipenuhi oleh subclass:
 *  - Mengimplementasikan {@see self::getName()} untuk memberikan
 *    identitas event yang unik dan stabil (biasanya digunakan sebagai
 *    routing key oleh EventBus/listener).
 *
 * Contoh subclass:
 * ```php
 * final class PartnerRegistered extends DomainEvent
 * {
 *     public function __construct(public readonly string $partnerId)
 *     {
 *         parent::__construct();
 *     }
 *
 *     public function getName(): string
 *     {
 *         return 'partner.registered';
 *     }
 * }
 * ```
 */
abstract class DomainEvent
{
    use Dispatchable;
    use InteractsWithSockets;

    /**
     * Timestamp (UNIX epoch, dalam detik) saat event ini dibuat.
     *
     * Nilai di-set otomatis pada constructor menggunakan {@see time()} dan
     * bersifat `readonly` sehingga tidak dapat dimodifikasi setelah event
     * di-instansiasi. Berguna untuk audit trail, urutan kronologis event,
     * dan debug.
     */
    public readonly int $occurredOn;

    /**
     * Membuat instance domain event baru.
     *
     * Constructor ini **wajib dipanggil** oleh subclass (via
     * `parent::__construct()`) agar properti {@see self::$occurredOn}
     * ter-inisialisasi dengan benar.
     */
    public function __construct()
    {
        $this->occurredOn = time();
    }

    /**
     * Mengembalikan nama event untuk keperluan identifikasi.
     *
     * Nama ini biasanya digunakan oleh EventBus/listener sebagai routing
     * key atau kunci registrasi handler. Disarankan menggunakan format
     * yang stabil dan deskriptif, misalnya `"partner.registered"` atau
     * `"order.placed"`.
     *
     * @return string Nama unik untuk event ini.
     */
    abstract public function getName(): string;
}
