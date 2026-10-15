<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Entities;

use Src\Shared\Framework\Domain\Events\DomainEvent;

/**
 * Class BaseAggregateRoot
 *
 * Kelas dasar untuk seluruh **Aggregate Root** pada arsitektur
 * Domain-Driven Design (DDD) proyek ini.
 *
 * Dalam DDD, sebuah *aggregate root* adalah entitas utama yang menjadi
 * pintu masuk terhadap sebuah *aggregate* (kumpulan objek domain yang
 * diperlakukan sebagai satu unit konsistensi). Aggregate root bertanggung
 * jawab untuk:
 * - Menjaga invariant bisnis di dalam aggregate.
 * - Menerbitkan {@see DomainEvent} setiap kali terjadi perubahan
 *   state yang bermakna secara bisnis (mis. `OrderPlaced`,
 *   `PartnerRegistered`, `InvoicePaid`).
 *
 * Kelas ini merupakan turunan {@see BaseEntity} sehingga secara otomatis
 * mendapatkan seluruh fitur entitas dasar (hidrasi otomatis dari Eloquent
 * Model, mekanisme *check point* / dirty check, pengelolaan `isNew`, audit
 * trail {@see \Src\Shared\Framework\Domain\ValueObjects\Timestamp}, dsb.).
 *
 * Selain itu, kelas ini menyediakan API pencatatan dan pelepasan domain
 * event yang lebih sederhana ({@see self::recordEvent()} dan
 * {@see self::pullEvents()}), yang berperan sebagai *shortcut* atas API
 * setara pada {@see BaseEntity} ({@see BaseEntity::record()},
 * {@see BaseEntity::pullDomainEvents()}). API pada kelas ini bersifat
 * **non-idempotent**: event yang sama dapat tercatat lebih dari satu kali
 * bila `recordEvent()` dipanggil berulang. Bila diperlukan perilaku
 * idempotent, gunakan {@see BaseEntity::record()} atau
 * {@see BaseEntity::recordLast()}.
 *
 * Kontrak umum bagi subclass:
 * - Menjamin invariant aggregate melalui method-method domainnya.
 * - Memanggil {@see self::recordEvent()} (atau varian pada BaseEntity)
 *   ketika terjadi perubahan state yang perlu diumumkan ke bounded
 *   context / listener lain.
 * - Membiarkan lapisan Application (mis. command handler / repository)
 *   memanggil {@see self::pullEvents()} untuk melepas event ke EventBus
 *   setelah aggregate berhasil dipersistensi.
 *
 * Contoh subclass:
 * ```php
 * final class Partner extends BaseAggregateRoot
 * {
 *     public static function register(string $name): self
 *     {
 *         $partner = new self();
 *         $partner->name = $name;
 *         $partner->recordEvent(new PartnerRegistered($partner->id));
 *
 *         return $partner;
 *     }
 * }
 * ```
 */
class BaseAggregateRoot extends BaseEntity
{
    /**
     * Buffer domain event yang telah dicatat oleh aggregate namun
     * belum dilepaskan (di-dispatch) ke EventBus.
     *
     * Properti ini menaungi (shadow) properti bernama sama pada
     * {@see BaseEntity}. Digunakan bersama {@see self::recordEvent()}
     * dan {@see self::pullEvents()}. Buffer akan dikosongkan secara
     * otomatis setelah dilepas melalui {@see self::pullEvents()}.
     *
     * @var DomainEvent[]
     */
    protected array $domainEvents = [];

    /**
     * Mencatat sebuah {@see DomainEvent} ke dalam buffer aggregate.
     *
     * Method ini bersifat **non-idempotent**: memanggilnya berkali-kali
     * dengan event yang sama akan menambahkan entri baru pada buffer.
     * Bila diperlukan pencatatan yang idempotent (menyimpan hanya satu
     * event per nama), gunakan {@see BaseEntity::record()} atau
     * {@see BaseEntity::recordLast()} yang tersedia dari kelas induk.
     *
     * Biasanya dipanggil dari dalam method domain pada aggregate root
     * setiap kali terjadi transisi state yang bermakna secara bisnis.
     *
     * @param  DomainEvent  $event  Event domain yang akan dicatat.
     */
    protected function recordEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Mengambil seluruh domain event yang tercatat pada aggregate
     * sekaligus mengosongkan buffer.
     *
     * Umumnya dipanggil oleh lapisan infrastruktur / application
     * (mis. repository atau command handler) segera setelah aggregate
     * berhasil dipersistensi, agar event dapat diteruskan ke EventBus
     * / listener / projector.
     *
     * Merupakan varian sederhana dari {@see BaseEntity::pullDomainEvents()}
     * yang beroperasi pada buffer lokal kelas ini.
     *
     * @return DomainEvent[] Daftar event yang sebelumnya tertampung
     *                       pada buffer, dalam urutan pencatatan.
     */
    public function pullEvents(): array
    {
        $events = $this->domainEvents;

        $this->domainEvents = [];

        return $events;
    }
}
