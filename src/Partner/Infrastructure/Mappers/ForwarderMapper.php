<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Mappers;

use App\Models\Forwarder as ForwarderModel;
use Src\Partner\Domain\Entities\Forwarder;
use Src\Partner\Domain\ReadModels\ForwarderRM;
use Src\Partner\Domain\ValueObjects\LegacyForwarderId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;

final readonly class ForwarderMapper
{
    /**
     * @param  ForwarderModel|array<string, mixed>  $data
     */
    public function toDomain(ForwarderModel|array $data): Forwarder
    {
        $attributes = $data instanceof ForwarderModel ? $data->getAttributes() : $data;

        $id = LegacyForwarderId::fromInt((int) ($attributes['id'] ?? 0));
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

        return Forwarder::reconstitute(
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
    public function toModel(Forwarder $forwarder): array
    {
        return [
            'company_id' => $forwarder->companyId->value(),
            'code' => $forwarder->code?->value(),
            'name' => $forwarder->name->value(),
            'phone' => $forwarder->contact->phone,
            'email' => $forwarder->contact->email,
            'address' => $forwarder->address->address,
            'postal_code' => $forwarder->address->postalCode,
            'tax_id' => $forwarder->taxIdentity?->taxId,
            'tax_id_clean' => $forwarder->taxIdentity?->taxIdClean,
            'tax_tku_id' => $forwarder->taxIdentity?->taxTkuId,
            'tax_name' => $forwarder->taxIdentity?->taxName,
            'tax_address' => $forwarder->taxIdentity?->taxAddress,
            'tax_buyer_document' => $forwarder->taxIdentity?->taxBuyerDocument,
            'tax_buyer_country' => $forwarder->taxIdentity?->taxBuyerCountry,
            'is_active' => $forwarder->isActive,
        ];
    }

    /**
     * @param  ForwarderModel|array<string, mixed>  $data
     */
    public function toReadModel(ForwarderModel|array $data): ForwarderRM
    {
        $attributes = $data instanceof ForwarderModel ? $data->getAttributes() : $data;

        return ForwarderRM::fromArray($attributes);
    }
}
