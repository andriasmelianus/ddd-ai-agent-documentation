<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\ValueObjects;

/**
 * Interface ValueObjectInterface
 *
 * Marker interface (kontrak tanpa method) yang menandai sebuah kelas
 * sebagai **Value Object** dalam terminologi Domain-Driven Design.
 *
 * Value Object bercirikan:
 *  - **Immutable**: setelah dibuat, state internal tidak berubah.
 *  - **Diidentifikasi oleh nilai**, bukan oleh identitas — dua VO dengan
 *    nilai yang sama dianggap ekuivalen.
 *  - **Self-validating**: memastikan validitas dirinya sendiri di constructor.
 *
 * Marker ini digunakan oleh {@see BaseEntityHydrater} dan bagian
 * infrastruktur lain untuk mendeteksi objek yang perlu diperlakukan
 * sebagai VO (misalnya saat melakukan hydration otomatis dari array/DB
 * dan re-composing dengan reflection).
 */
interface ValueObjectInterface {}
