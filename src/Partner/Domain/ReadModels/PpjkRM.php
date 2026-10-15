<?php

declare(strict_types=1);

namespace Src\Partner\Domain\ReadModels;

use JsonSerializable;

/**
 * Class PpjkRM
 *
 * Immutable ReadModel projection for PPJK queries.
 */
readonly class PpjkRM implements JsonSerializable
{
    public function __construct(
        public int $id,
        public int $companyId,
        public ?string $code = null,
        public string $name = '',
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $address = null,
        public ?string $postalCode = null,
        public ?string $taxId = null,
        public ?string $taxIdClean = null,
        public ?string $taxTkuId = null,
        public ?string $taxName = null,
        public ?string $taxAddress = null,
        public ?string $taxBuyerDocument = null,
        public ?string $taxBuyerCountry = null,
        public bool $isActive = true,
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
            companyId: (int) ($data['company_id'] ?? 0),
            code: isset($data['code']) ? (string) $data['code'] : null,
            name: (string) ($data['name'] ?? ''),
            phone: isset($data['phone']) ? (string) $data['phone'] : null,
            email: isset($data['email']) ? (string) $data['email'] : null,
            address: isset($data['address']) ? (string) $data['address'] : null,
            postalCode: isset($data['postal_code']) ? (string) $data['postal_code'] : null,
            taxId: isset($data['tax_id']) ? (string) $data['tax_id'] : null,
            taxIdClean: isset($data['tax_id_clean']) ? (string) $data['tax_id_clean'] : null,
            taxTkuId: isset($data['tax_tku_id']) ? (string) $data['tax_tku_id'] : null,
            taxName: isset($data['tax_name']) ? (string) $data['tax_name'] : null,
            taxAddress: isset($data['tax_address']) ? (string) $data['tax_address'] : null,
            taxBuyerDocument: isset($data['tax_buyer_document']) ? (string) $data['tax_buyer_document'] : null,
            taxBuyerCountry: isset($data['tax_buyer_country']) ? (string) $data['tax_buyer_country'] : null,
            isActive: (bool) ($data['is_active'] ?? true),
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
            'company_id' => $this->companyId,
            'code' => $this->code,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'postal_code' => $this->postalCode,
            'tax_id' => $this->taxId,
            'tax_id_clean' => $this->taxIdClean,
            'tax_tku_id' => $this->taxTkuId,
            'tax_name' => $this->taxName,
            'tax_address' => $this->taxAddress,
            'tax_buyer_document' => $this->taxBuyerDocument,
            'tax_buyer_country' => $this->taxBuyerCountry,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
