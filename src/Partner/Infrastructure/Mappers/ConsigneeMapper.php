<?php

declare(strict_types=1);

namespace Src\Partner\Infrastructure\Mappers;

use App\Models\Consignee as ConsigneeModel;
use Src\Partner\Domain\Entities\Consignee;
use Src\Partner\Domain\ReadModels\ConsigneeRM;
use Src\Partner\Domain\ValueObjects\LegacyConsigneeId;
use Src\Partner\Domain\ValueObjects\PartnerAddress;
use Src\Partner\Domain\ValueObjects\PartnerCode;
use Src\Partner\Domain\ValueObjects\PartnerCompanyId;
use Src\Partner\Domain\ValueObjects\PartnerContact;
use Src\Partner\Domain\ValueObjects\PartnerName;
use Src\Partner\Domain\ValueObjects\PartnerTaxIdentity;
use Src\Partner\Domain\ValueObjects\TransactionTypeCode;

final readonly class ConsigneeMapper
{
    /**
     * @param  ConsigneeModel|array<string, mixed>  $data
     */
    public function toDomain(ConsigneeModel|array $data): Consignee
    {
        $attributes = $data instanceof ConsigneeModel ? $data->getAttributes() : $data;

        $id = LegacyConsigneeId::fromInt((int) ($attributes['id'] ?? 0));
        $companyId = PartnerCompanyId::fromInt((int) ($attributes['company_id'] ?? 0));
        $name = PartnerName::fromString(
            (string) ($attributes['name'] ?? ''),
            isset($attributes['name_key']) ? (string) $attributes['name_key'] : null,
        );

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

        $transactionTypeCode = TransactionTypeCode::fromString(
            isset($attributes['kd_jenis_transaksi']) ? (string) $attributes['kd_jenis_transaksi'] : null
        );

        $isActive = (bool) ($attributes['is_active'] ?? true);

        return Consignee::reconstitute(
            id: $id,
            companyId: $companyId,
            name: $name,
            code: $code,
            contact: $contact,
            address: $address,
            taxIdentity: $taxIdentity,
            transactionTypeCode: $transactionTypeCode,
            isActive: $isActive,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toModel(Consignee $consignee): array
    {
        return [
            'company_id' => $consignee->companyId->value(),
            'code' => $consignee->code?->value(),
            'name' => $consignee->name->value(),
            'name_key' => $consignee->name->key(),
            'phone' => $consignee->contact->phone,
            'email' => $consignee->contact->email,
            'address' => $consignee->address->address,
            'postal_code' => $consignee->address->postalCode,
            'tax_id' => $consignee->taxIdentity?->taxId,
            'tax_id_clean' => $consignee->taxIdentity?->taxIdClean,
            'tax_tku_id' => $consignee->taxIdentity?->taxTkuId,
            'tax_name' => $consignee->taxIdentity?->taxName,
            'tax_address' => $consignee->taxIdentity?->taxAddress,
            'tax_buyer_document' => $consignee->taxIdentity?->taxBuyerDocument,
            'tax_buyer_country' => $consignee->taxIdentity?->taxBuyerCountry,
            'kd_jenis_transaksi' => $consignee->transactionTypeCode->value(),
            'is_active' => $consignee->isActive,
        ];
    }

    /**
     * @param  ConsigneeModel|array<string, mixed>  $data
     */
    public function toReadModel(ConsigneeModel|array $data): ConsigneeRM
    {
        $attributes = $data instanceof ConsigneeModel ? $data->getAttributes() : $data;

        return ConsigneeRM::fromArray($attributes);
    }
}
