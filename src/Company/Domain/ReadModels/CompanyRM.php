<?php

declare(strict_types=1);

namespace Src\Company\Domain\ReadModels;

use JsonSerializable;

/**
 * Class CompanyRM
 *
 * Immutable ReadModel projection for Company queries.
 */
readonly class CompanyRM implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $phone1 = null,
        public ?string $phone2 = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $subDistrict = null,
        public ?string $district = null,
        public ?string $province = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $taxId = null,
        public ?string $taxIdClean = null,
        public ?string $taxTkuId = null,
        public ?string $taxName = null,
        public ?string $tpsCode = null,
        public ?string $tpsWarehouseCode = null,
        public ?string $decreeNumber = null,
        public ?string $decreeDate = null,
        public ?float $warehouseCapacity = null,
        public bool $isActive = true,
        public bool $hasCeisaConfigured = false,
        public bool $hasApiConfigured = false,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            phone1: isset($data['phone_number1']) ? (string) $data['phone_number1'] : null,
            phone2: isset($data['phone_number2']) ? (string) $data['phone_number2'] : null,
            address: isset($data['address1']) ? (string) $data['address1'] : null,
            city: isset($data['city']) ? (string) $data['city'] : null,
            subDistrict: isset($data['sub_district']) ? (string) $data['sub_district'] : null,
            district: isset($data['district']) ? (string) $data['district'] : null,
            province: isset($data['province']) ? (string) $data['province'] : null,
            country: isset($data['country']) ? (string) $data['country'] : null,
            postalCode: isset($data['postal_code']) ? (string) $data['postal_code'] : null,
            taxId: isset($data['tax_id']) ? (string) $data['tax_id'] : null,
            taxIdClean: isset($data['tax_id_clean']) ? (string) $data['tax_id_clean'] : null,
            taxTkuId: isset($data['tax_tku_id']) ? (string) $data['tax_tku_id'] : null,
            taxName: isset($data['tax_name']) ? (string) $data['tax_name'] : null,
            tpsCode: isset($data['tps_code']) ? (string) $data['tps_code'] : null,
            tpsWarehouseCode: isset($data['tps_warehouse_code']) ? (string) $data['tps_warehouse_code'] : null,
            decreeNumber: isset($data['decree_number']) ? (string) $data['decree_number'] : null,
            decreeDate: isset($data['decree_date']) ? (string) $data['decree_date'] : null,
            warehouseCapacity: isset($data['warehouse_capacity']) ? (float) $data['warehouse_capacity'] : null,
            isActive: (bool) ($data['is_active'] ?? true),
            hasCeisaConfigured: ! empty($data['ceisa40_username']),
            hasApiConfigured: ! empty($data['api_key']),
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone1' => $this->phone1,
            'phone2' => $this->phone2,
            'address' => $this->address,
            'city' => $this->city,
            'sub_district' => $this->subDistrict,
            'district' => $this->district,
            'province' => $this->province,
            'country' => $this->country,
            'postal_code' => $this->postalCode,
            'tax_id' => $this->taxId,
            'tax_id_clean' => $this->taxIdClean,
            'tax_tku_id' => $this->taxTkuId,
            'tax_name' => $this->taxName,
            'tps_code' => $this->tpsCode,
            'tps_warehouse_code' => $this->tpsWarehouseCode,
            'decree_number' => $this->decreeNumber,
            'decree_date' => $this->decreeDate,
            'warehouse_capacity' => $this->warehouseCapacity,
            'is_active' => $this->isActive,
            'has_ceisa_configured' => $this->hasCeisaConfigured,
            'has_api_configured' => $this->hasApiConfigured,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
