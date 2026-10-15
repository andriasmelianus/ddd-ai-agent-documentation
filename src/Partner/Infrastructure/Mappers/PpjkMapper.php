<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Mappers;

use App\Models\Ppjk as PpjkModel;
use Src\Partner\Domain\Entities\Ppjk;
use Src\Partner\Domain\ReadModels\PpjkRM;
use Src\Partner\Domain\ValueObjects\LegacyPpjkId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;

final readonly class PpjkMapper
{
    /**
     * @param  PpjkModel|array<string, mixed>  $data
     */
    public function toDomain(PpjkModel|array $data): Ppjk
    {
        $attributes = $data instanceof PpjkModel ? $data->getAttributes() : $data;

        $id = LegacyPpjkId::fromInt((int) ($attributes['id'] ?? 0));
        $companyId = PartnerCompanyId::fromInt((int) ($attributes['company_id'] ?? 0));
        $name = PartnerName::fromString((string) ($attributes['name'] ?? ''));

        $code = isset($attributes['code']) && trim((string) $attributes['code']) !== ''
            ? PartnerCode::fromString((string) $attributes['code'])
            : null;

        $contact = PartnerContact::fromPrimitives(
            phone: isset($attributes['phone']) ? (string) $attributes['phone'] : null,
            email: isset($attributes['email']) ? (string) $attributes['email'] : null,
        );

        $address = PartnerAddress::fromPrimitives(
            address: isset($attributes['address']) ? (string) $attributes['address'] : null,
            postalCode: isset($attributes['postal_code']) ? (string) $attributes['postal_code'] : null,
        );

        $taxIdentity = PartnerTaxIdentity::fromPrimitives(
            taxId: isset($attributes['tax_id']) ? (string) $attributes['tax_id'] : null,
            taxTkuId: isset($attributes['tax_tku_id']) ? (string) $attributes['tax_tku_id'] : null,
            taxName: isset($attributes['tax_name']) ? (string) $attributes['tax_name'] : null,
            taxAddress: isset($attributes['tax_address']) ? (string) $attributes['tax_address'] : null,
            taxBuyerDocument: isset($attributes['tax_buyer_document']) ? (string) $attributes['tax_buyer_document'] : null,
            taxBuyerCountry: isset($attributes['tax_buyer_country']) ? (string) $attributes['tax_buyer_country'] : null,
        );

        $isActive = (bool) ($attributes['is_active'] ?? true);

        return Ppjk::reconstitute(
            id: $id,
            companyId: $companyId,
            name: $name,
            code: $code,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            isActive: $isActive,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toModel(Ppjk $ppjk): array
    {
        return [
            'company_id' => $ppjk->companyId->value(),
            'code' => $ppjk->code?->value(),
            'name' => $ppjk->name->value(),
            'phone' => $ppjk->contact->phone,
            'email' => $ppjk->contact->email,
            'address' => $ppjk->address->address,
            'postal_code' => $ppjk->address->postalCode,
            'tax_id' => $ppjk->taxIdentity?->taxId,
            'tax_id_clean' => $ppjk->taxIdentity?->taxIdClean,
            'tax_tku_id' => $ppjk->taxIdentity?->taxTkuId,
            'tax_name' => $ppjk->taxIdentity?->taxName,
            'tax_address' => $ppjk->taxIdentity?->taxAddress,
            'tax_buyer_document' => $ppjk->taxIdentity?->taxBuyerDocument,
            'tax_buyer_country' => $ppjk->taxIdentity?->taxBuyerCountry,
            'is_active' => $ppjk->isActive,
        ];
    }

    /**
     * @param  PpjkModel|array<string, mixed>  $data
     */
    public function toReadModel(PpjkModel|array $data): PpjkRM
    {
        $attributes = $data instanceof PpjkModel ? $data->getAttributes() : $data;

        return PpjkRM::fromArray($attributes);
    }
}
