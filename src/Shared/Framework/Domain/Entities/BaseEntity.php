<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Domain\Entities;

use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Src\Shared\Framework\Domain\Events\DomainEvent;
use Src\Shared\Framework\Domain\ValueObjects\IdentifierInterface;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;
use Src\Shared\Framework\Helpers\MixedHelper;
use Src\Shared\Framework\Helpers\ObjectHelper;
use Src\Shared\Framework\Helpers\TraitDynamicProperties;

use function count;
use function in_array;
use function is_array;
use function is_object;

/**
 * Basis kelas untuk seluruh Entitas Domain (Aggregate Root maupun Entity biasa)
 * pada arsitektur DDD proyek ini.
 *
 * Kelas ini menyediakan kemampuan yang umum dibutuhkan oleh entitas:
 *  - Pencatatan {@see DomainEvent} yang dihasilkan oleh entitas.
 *  - Serialisasi properti publik menjadi array primitif (siap disimpan / dikirim).
 *  - Hidrasi otomatis dari Eloquent {@see Model} menggunakan reflection
 *    melalui {@see BaseEntityHydrater} — mendukung Value Object, Backed Enum,
 *    Identifier, dan Carbon.
 *  - Mekanisme "check point" untuk mendeteksi perubahan state (dirty check)
 *    setelah entitas terhidrasi dari persistensi.
 *  - Pengelolaan status `isNew` dan {@see Timestamp} audit trail.
 *
 * @property IdentifierInterface $id
 */
abstract class BaseEntity
{
    use TraitDynamicProperties;

    /**
     * Snapshot state entitas pada saat check point diambil, digunakan untuk
     * membandingkan apakah entitas mengalami perubahan.
     *
     * @var array<string,mixed>
     */
    private array $checkPoint;

    /**
     * Kumpulan domain event yang telah tercatat namun belum dilepaskan
     * (di-dispatch) ke event bus.
     *
     * @var DomainEvent[]
     */
    protected array $domainEvents = [];

    /**
     * Menandakan apakah entitas ini baru dibuat (belum pernah dipersistensi).
     */
    protected bool $isNew = true;

    /**
     * Informasi audit trail (created/updated at & by) bila tersedia.
     */
    protected ?Timestamp $timestamps = null;

    /**
     * Mengambil snapshot state saat ini sebagai baseline untuk pengecekan
     * perubahan berikutnya melalui {@see hasChange()}.
     */
    protected function makeCheckPoint(): void
    {
        $this->checkPoint = $this->serialize();
    }

    /**
     * Mengecek apakah state entitas berubah dibandingkan snapshot terakhir
     * yang diambil oleh {@see makeCheckPoint()}.
     *
     * @return bool True bila terdapat perubahan; false bila identik.
     */
    public function hasChange(): bool
    {
        return MixedHelper::safeJson($this->checkPoint) !== MixedHelper::safeJson($this->serialize());
    }

    /**
     * Membangun (hidrasi) instance entitas dari Eloquent Model secara otomatis
     * berdasarkan reflection tipe properti kelas anak.
     *
     * Alur kerja:
     *  1. Membuat instance entitas tanpa memanggil constructor.
     *  2. Menandai entitas sebagai bukan "new" (sudah ada di persistensi).
     *  3. Mengisi properti dari {@see Model::getAttributes()} menggunakan
     *     {@see BaseEntityHydrater::calculateAttributesFromArray()}.
     *  4. Menerapkan mapping manual bila ada (mengabaikan hasil otomatis
     *     untuk kunci-kunci tersebut).
     *
     * @param  Model  $model  Model Eloquent sumber data.
     * @param  array<string,mixed>  $manualMappingArray  Override manual: kunci = nama properti, nilai = value siap pakai.
     *                                                   TODO: dukungan nested ValueObjects.
     * @return static Instance entitas yang telah terhidrasi.
     *
     * @throws RuntimeException Bila terjadi kegagalan reflection.
     */
    public static function autoHydrate(Model $model, array $manualMappingArray = []): self
    {

        $class = new ReflectionClass(static::class);
        $array = [];
        try {
            $element = $class->newInstanceWithoutConstructor();
            $element->setIsNew(false);
            $array = $model->getAttributes();
            $element->assignTimestamp($array);

            if (count($manualMappingArray)) {
                foreach ($manualMappingArray as $key => $item) {
                    unset($array[$key]);
                }
            }

            $params = BaseEntityHydrater::calculateAttributesFromArray($array, $class);

            foreach ($params as $key => $value) {
                $element->{$key} = $value;
                unset($array[$key]);
            }
            foreach ($manualMappingArray as $key => $item) {
                $element->{$key} = $item;
            }
        } catch (ReflectionException $e) {
            Log::error($e->getMessage());
            Log::error(print_r($array, true));
            echo $e->getMessage();
            throw new RuntimeException($e->getMessage());
        }

        return $element;
    }

    /**
     * Menyusun {@see Timestamp} entitas dari atribut mentah Eloquent bila
     * salah satu dari created_at / updated_at / createdBy / updatedBy hadir.
     *
     * @param  array<string, mixed>  $attributes  Atribut mentah dari Eloquent Model.
     */
    private function assignTimestamp(array $attributes): void
    {
        if (isset($attributes['created_at']) || isset($attributes['updated_at']) || isset($attributes['createdBy']) || isset($attributes['updatedBy'])) {
            $createdAt = isset($attributes['created_at']) ? strtotime(MixedHelper::getString($attributes['created_at'])) : null;
            $updatedAt = isset($attributes['updated_at']) ? strtotime(MixedHelper::getString($attributes['updated_at'])) : null;
            $createdBy = isset($attributes['createdBy']) ? MixedHelper::getString($attributes['createdBy']) : null;
            $updatedBy = isset($attributes['updatedBy']) ? MixedHelper::getString($attributes['updatedBy']) : null;

            $this->timestamps = new Timestamp(
                createdBy: $createdBy,
                updatedBy: $updatedBy,
                createdAt: $createdAt ?: null,
                updatedAt: $updatedAt ?: null,
            );
        }
    }

    /**
     * Alias kompatibilitas (Copilot Compatibility) untuk {@see pullDomainEvents()}.
     *
     * @return array<DomainEvent> Daftar event yang tertampung, sekaligus mengosongkan buffer.
     */
    final public function releaseEvents(): array
    {
        return $this->pullDomainEvents();
    }

    /**
     * Mencatat sebuah domain event, memastikan hanya event terbaru
     * dari tipe yang sama yang tersimpan (event lama dengan nama sama dihapus).
     *
     * @param  DomainEvent  $domainEvent  Event yang akan dicatat.
     */
    final protected function recordOnce(DomainEvent $domainEvent): void
    {
        foreach ($this->domainEvents as $key => $event) {
            if ($event->getName() === $domainEvent->getName()) {
                unset($this->domainEvents[$key]);
            }
        }
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * Mengambil seluruh domain event yang tercatat sekaligus mengosongkan buffer.
     *
     * @return array<DomainEvent> Daftar event yang sebelumnya tercatat.
     */
    public function pullDomainEvents(): array
    {
        $domainEvents = $this->domainEvents;
        $this->resetDomainEvents();

        return $domainEvents;
    }

    /**
     * Mengosongkan buffer domain event tanpa mengembalikannya.
     */
    final public function resetDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    /**
     * Mengecek status "new" entitas (true bila belum dipersistensi).
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * Mengeset status "new" entitas secara eksplisit.
     *
     * @param  bool  $isNew  True bila entitas dianggap baru; false bila sudah ada di persistensi.
     */
    public function setIsNew(bool $isNew = true): void
    {
        $this->isNew = $isNew;
    }

    /**
     * Mengembalikan objek {@see Timestamp} entitas bila tersedia.
     */
    public function timestamps(): ?Timestamp
    {
        return $this->timestamps;
    }

    /**
     * Mencatat sebuah domain event bila event dengan nama sama belum ada
     * di buffer (idempotent record — event pertama yang dipertahankan).
     *
     * @param  DomainEvent  $domainEvent  Event yang akan dicatat.
     */
    final protected function record(DomainEvent $domainEvent): void
    {
        foreach ($this->domainEvents as $event) {
            if ($event->getName() === $domainEvent->getName()) {
                return;
            }
        }
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * Mencatat domain event dan memastikan hanya event dengan waktu tercatat
     * paling akhir dari tipe yang sama yang bertahan (menggantikan yang lama).
     *
     * @param  DomainEvent  $domainEvent  Event yang akan dicatat.
     */
    final protected function recordLast(DomainEvent $domainEvent): void
    {
        foreach ($this->domainEvents as $key => $event) {
            if ($event->getName() === $domainEvent->getName()) {
                unset($this->domainEvents[$key]);
            }
        }
        $this->domainEvents[] = $domainEvent;
    }

    /**
     * Mengubah entitas menjadi array primitif siap simpan / kirim.
     *
     * Transformasi yang dilakukan tiap nilai:
     *  - array               → JSON string aman via {@see MixedHelper::safeJson()}.
     *  - {@see BackedEnum}   → nilai (`->value`) dari enum.
     *  - {@see Carbon}       → timestamp Unix.
     *  - {@see IdentifierInterface} → hasil `getValue()` (bisa string/int).
     *  - object lain         → JSON string aman.
     *
     * @param  array<string>|null  $fieldList  Daftar field yang ingin di-serialisasi (null = semua public).
     * @param  array<string>|null  $fieldIgnored  Daftar field yang harus diabaikan dari hasil.
     * @return array<string,mixed> Representasi array dari entitas.
     */
    public function serialize(?array $fieldList = null, ?array $fieldIgnored = null): array
    {
        $attributesTransformed = [];
        $attributes = $this->calculateAttributeArray($this->getAttributesWithValues(), $fieldList);

        /**
         * @var string $key
         * @var mixed $value
         */
        foreach ($attributes as $key => $value) {
            $valueTransformed = $value;
            if (is_array($value)) {
                $valueTransformed = MixedHelper::safeJson($value);
                $attributesTransformed[$key] = $valueTransformed;

                continue;
            }
            if ($value instanceof BackedEnum) {
                $valueTransformed = $value->value;
                $attributesTransformed[$key] = $valueTransformed;

                continue;
            }
            if ($value instanceof Carbon) {
                $valueTransformed = $value->timestamp;
                $attributesTransformed[$key] = $valueTransformed;

                continue;
            }
            if (is_object($value) && ObjectHelper::implements($value, IdentifierInterface::class)) {
                /** @phpstan-ignore-next-line */
                $valueTransformed = $value->getValue();
            } elseif (is_object($value)) {
                $valueTransformed = MixedHelper::safeJson($value);
            }
            $attributesTransformed[$key] = $valueTransformed;
        }

        if ($fieldIgnored) {
            foreach ($attributesTransformed as $key => $attribute) {
                if (in_array($key, $fieldIgnored, true)) {
                    unset($attributesTransformed[$key]);
                }
            }
        }

        return $attributesTransformed;
    }

    /**
     * Memfilter array atribut menjadi hanya kunci yang dicantumkan di $fieldList.
     * Bila $fieldList null, kembalikan array apa adanya.
     *
     * @param  array<string,mixed>  $attributeList  Array atribut sumber.
     * @param  array<string>|null  $fieldList  Daftar kunci yang diinginkan.
     * @return array<string,mixed> Hasil filter.
     */
    private function calculateAttributeArray(array $attributeList, ?array $fieldList): array
    {
        if ($fieldList === null) {
            return $attributeList;
        }
        $resArray = [];
        foreach ($fieldList as $field) {
            if (isset($attributeList[$field])) {
                $resArray[$field] = $attributeList[$field];
            }
        }

        return $resArray;
    }

    /**
     * Mengembalikan seluruh nilai properti publik dari entitas dalam bentuk
     * asosiatif [nama => nilai].
     *
     * @param  array<string>|null  $names  Daftar nama properti yang akan diambil (null = seluruh public property).
     * @param  array<string>  $except  Daftar properti yang harus dikecualikan dari hasil.
     * @return array<string,mixed> Peta nama properti ke nilainya.
     */
    public function getAttributesWithValues(?array $names = null, array $except = []): array
    {
        $values = [];
        if ($names === null) {
            $names = $this->getPublicAttributeArray();
        }
        foreach ($names as $name) {
            $values[$name] = $this->{$name};
        }
        foreach ($except as $name) {
            unset($values[$name]);
        }

        return $values;
    }

    /**
     * Mendapatkan daftar nama seluruh properti publik non-static entitas
     * menggunakan reflection.
     *
     * @return array<string> Daftar nama properti publik.
     */
    public function getPublicAttributeArray(): array
    {
        $class = new ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (! $property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * Menandai entitas telah selesai terhidrasi dari persistensi
     * (mengeset `isNew` ke false) dan mengembalikan dirinya untuk chaining.
     *
     * @return static
     */
    public function hydrated(): self
    {
        $this->isNew = false;

        return $this;
    }

    /**
     * Mengisi ulang audit trail {@see Timestamp} dari atribut Eloquent Model.
     *
     * @param  Model  $model  Model Eloquent sumber data audit trail.
     * @return static
     */
    public function hydrateTimestamps(Model $model): self
    {
        $this->assignTimestamp($model->getAttributes());

        return $this;
    }
}
