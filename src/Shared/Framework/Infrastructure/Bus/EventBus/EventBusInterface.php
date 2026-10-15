<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\EventBus;

use Src\Shared\Framework\Domain\Events\DomainEvent;

/**
 * Kontrak (interface) Event Bus untuk mempublikasikan Domain Event.
 *
 * Event Bus bertanggung jawab mendistribusikan Domain Event yang dihasilkan
 * oleh Aggregate/Entity ke seluruh subscriber/listener yang terdaftar.
 * Pola ini merupakan bagian dari arsitektur Domain-Driven Design (DDD)
 * dan CQRS, di mana perubahan state pada domain dikomunikasikan melalui
 * event yang bersifat immutable.
 *
 * Anotasi `@method __invoke(DomainEvent $event)` menandakan bahwa
 * implementasi bus juga dapat dipanggil sebagai invokable (callable) untuk
 * mempublikasikan satu event tunggal, meski kontrak utamanya adalah
 * {@see EventBusInterface::publishEvents()} untuk publikasi batch.
 *
 * @method __invoke(DomainEvent $event)
 */
interface EventBusInterface
{
    /**
     * Mempublikasikan sekumpulan Domain Event ke seluruh listener terdaftar.
     *
     * Metode ini biasanya dipanggil setelah persistence berhasil dilakukan
     * (mis. di dalam Command Handler setelah repository melakukan `save()`),
     * dengan input berupa hasil dari `Entity::pullDomainEvents()` atau
     * `Entity::releaseEvents()`.
     *
     * @param  array<DomainEvent>  $events  Daftar Domain Event yang akan dipublikasikan.
     */
    public function publishEvents(array $events): void;
}
